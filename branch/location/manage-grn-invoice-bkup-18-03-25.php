<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/branch/func-grn-controller.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../../public/assets/filepond/filepond-plugin-image-preview.min.css">
<link rel="stylesheet" href="../../public/assets/filepond/filepond.min.css">
<script src="../../public/assets/filepond/filepond-plugin-file-encode.min.js"></script>
<script src="../../public/assets/filepond/filepond-plugin-file-validate-size.min.js"></script>
<script src="../../public/assets/filepond/filepond-plugin-image-exif-orientation.min.js"></script>
<script src="../../public/assets/filepond/filepond-plugin-image-preview.min.js"></script>
<script src="../../public/assets/filepond/filepond.min.js"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<!-- <script src="https://riversun.github.io/jsframe/jsframe.js"></script> -->
<link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">


<style>
    .grn-invoice-table td {
        padding: 3px !important;
        text-align: center !important;
        background: none;
        border-color: transparent !important;

    }

    .grn-invoice-table tr td:nth-child(1) {
        text-align: left !important;
    }

    .grn-invoice-table td p {
        background: #fff;
        padding: 3px;
        border-radius: 5px;
    }

    .float-right {
        float: right;
    }

    .grn-upload-card {
        background: #c4d8eb;
        width: 100%;
        /* box-shadow: 1px 7px 5px -1px #989898; */
        margin: 15px auto 0px;
        border-radius: 10px 10px 0 0;
    }

    .mismatch-grn-table {
        border-radius: 0 0 10px 10px;
    }

    .card-box {
        /* display: flex; */
        justify-content: space-between;
        align-items: center;
        margin: 7px 5px;
        gap: 12px;
    }

    input.upload-button.btn.btn-primary {
        position: absolute;
        top: 10px;
        left: -76px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        opacity: 0;
    }

    .grn-invoice-modal .modal-dialog {
        max-width: 75%;
    }

    div#jsFrame_fixed_37b7a591-1d6c-4ab5-b8ea-19b45bebfaea {
        z-index: 9999 !important;
    }

    .is-grn-invoice .grn-po-post {
        background: #003060 !important;
        color: #fff;
    }


    lottie-player.ocr-reading-animation {
        position: fixed;
        z-index: 9;
        top: 24%;
        left: 42%;
        max-width: 250px;
    }

    body.sidebar-mini.layout-fixed.sidebar-collapse .row.box.grn-box .md.hydrated {
        display: none;
    }

    .row.create-grn-step {
        display: none;
    }

    .ocr-reading-animation {
        object-fit: contain;
    }

    .row.grnCreate {
        /* height: calc(100vh - (100px + 120px)); */
        height: auto;
        min-height: 100%;
        margin: 8em auto 10px;
    }

    .result-status-text-icon {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .swing {
        transform-origin: top left;
        animation: swing 2s ease infinite;
    }

    @keyframes swing {
        20% {
            transform: rotate(15deg);
        }

        40% {
            transform: rotate(-10deg);
        }

        60% {
            transform: rotate(5deg);
        }

        80% {
            transform: rotate(-5deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    .grn-robot-icon {

        position: relative;

        left: -100px;

    }

    .loading-image h1 {
        position: relative;
        top: 0;
        left: 0;
        z-index: 9;
        max-width: 500px;
        padding: 0 50px px;
        margin: 23px auto;
        text-align: center;
        text-decoration: none;
        background: #d8d8d8;
        transform: translate(0px, 500px);
        backdrop-filter: blur(8px);
        border-radius: 13px;
        /* width: calc(100% - 1108px); */
    }

    .loading-image h1 a {
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        color: #003060;
        padding: 0px 20px;
        line-height: 4.5rem;
    }


    @media (max-width: 768px) {
        .grn-robot-icon {
            position: relative;
            left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
        }

        .grn-robot-icon img {
            max-width: 150px;
            margin: 20px auto;
        }


        .row.grnCreate {
            height: 100%;
            transform: translate(0px, -80px);
            flex-direction: column-reverse;
        }

        ion-icon.upload-icon-absolute.md.hydrated {
            display: none;
        }

        .filepond--drop-label.filepond--drop-label label {
            left: 0 !important;
            padding-top: 10px;
        }

        span.filepond--label-action {
            margin-left: 0;
            line-height: 3rem;
        }

        .filepond--root .filepond--drop-label {
            min-height: 5.75em !important;
        }

        .fixed-grn-limit {
            position: relative !important;
            border-radius: 12px !important;
            top: 0 !important;
            text-align: center;
            max-width: 300px;
            margin: 20px auto 10px;
        }

        .row.box.grn-box p.heading-line {
            margin: 0 0px;
        }

        .grn-notes {
            margin: 12px 7px !important;
        }

        lottie-player.ocr-reading-animation {
            position: fixed;
            left: 34%;
        }
    }

    @media (max-width: 575px) {
        .row.box.grn-box {
            width: 100%;
        }

        .row.grnCreate {
            height: auto;
            transform: translate(0px, 0px);
            flex-direction: column-reverse;
            margin-top: 50px;
        }

        .grn-robot-icon {
            position: relative;
            left: 0;
        }

        .grn-robot-icon img {
            max-width: 150px;
            margin: 20px auto;
        }

        .fixed-grn-limit {
            top: 75px !important;
            right: 0 !important;
        }

        .grn-notes p {
            font-size: 10px !important;
            margin-bottom: 0;
            line-height: 1.2rem;
        }

        .grn-notes {
            padding-left: 0 !important;
            margin-bottom: 3em;
        }

        .fixed-grn-limit {
            position: relative !important;
            border-radius: 12px !important;
            top: 0 !important;
        }

        lottie-player.ocr-reading-animation {
            position: fixed;
            z-index: 9;
            top: 32%;
            left: 76px !important;
            max-width: 250px;

        }

        .loading-image h1 {
            transform: translate(0px, 530px);
        }

        .loading-image h1 a {
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            line-height: 3rem;
            color: #003060;
        }
    }

    .grn-notes {
        padding-left: 0;
        margin-top: 0;
        font-weight: 600;
        margin: 10px 40px;
    }

    .grn-notes hr {
        max-width: 200px;
        margin: 7px 0;
    }

    .grn-notes ul li {
        list-style-type: disc;
        margin-left: 1em;
    }

    .grn-notes p {
        font-size: 10px !important;
        line-height: 1.5rem;
    }

    .fixed-grn-limit {
        position: fixed;
        right: 0;
        top: 85%;
        background: #003060;
        padding: 15px;
        border-radius: 10px 0 0 10px;
        color: #fff;
        transition: ease-out;
        transition-delay: 0.2s;
        z-index: 99;
    }

    .grn-type {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .auto-grn,
    .po-grn {
        display: flex;
        align-items: center;
        gap: 7px;
        width: 150px;
        background: #003060;
        margin: 12px 0;
        color: #fff;
        padding: 10px;
        justify-content: center;
        border-radius: 7px;
        cursor: pointer;
    }

    .is-grn-invoice ul.select-grn-type button .grn-tab {
        background: #fff;
        border: 1px solid #00306070;
        color: #003060;
    }

    .is-grn-invoice ul.select-grn-type button.active .grn-tab {
        display: flex;
        align-items: center;
        gap: 7px;
        width: 150px;
        background: #003060;
        margin: 12px 0;
        color: #fff;
        padding: 10px;
        justify-content: center;
        border-radius: 7px;
        cursor: pointer;
    }

    .auto-grn input[type="radio"],
    .po-grn input[type="radio"] {
        accent-color: #0253a4;
        padding: 10px !important;
    }

    .row.hsn-add input {
        height: 30px;
        font-size: 12px;
    }

    .btns-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .btns-group button {
        width: 100%;
    }
</style>

<div class="loading-image" id="showme" style="display: none;">
    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_ofa3xwo7.json" class="ocr-reading-animation" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay>

    </lottie-player>

    <h1>
        <a href="" class="typewrite" data-period="800" data-type='[ "Relax for a while... !", "Back to your seat for few minutes.....", "I will handle it" ]'>
            <span class="wrap"></span>
        </a>
    </h1>
</div>

<?php
if (!isset($_SESSION['ocr_limitation'])) {
    $_SESSION['ocr_limitation'] = 200;
}
$ocrLimits = $_SESSION['ocr_limitation'];
?>

<div class="content-wrapper is-grn-invoice blur">
    <section class="content">
        <div class="container-fluid">

            <ul class="nav nav-pills select-grn-type mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                        <div class="auto-grn grn-tab">
                            <!-- <input type="radio" id="auto" name="type" value="auto" checked> -->
                            <label for="auto" class="mb-0">Automatic GRN</label>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                        <div class="po-grn grn-tab">
                            <!-- <input type="radio" id="po" name="type" value="po"> -->
                            <label for="po" class="mb-0">GRN through PO</label>
                        </div>
                    </button>
                </li>
            </ul>
            <div class="tab-content grn-type-tab" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="fixed-grn-limit">
                        <p class="text-xs mb-0">Your available limit balance <span class="font-bold text-sm ocrLimits"><?php echo $ocrLimits; ?></span> doc.</p>
                    </div>
                    <!-- Robot Div Start -->
                    <form id="upload_form" method="POST" enctype="multipart/form-data">
                        <div class="row grnCreate">
                            <div class="col-lg-8 col-md-12 col-sm-12">
                                <div class="row box grn-box">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <p class="font-bold text-xs heading-line">Multiple Upload</p>
                                        <input id="upload_grn_input" type="file" class="filepond" name="grnInvoiceFilemultiple[]" multiple data-allow-reorder="true">
                                        <input id="upload_grn_input" type="file" class="filepond" name="grnInvoiceFilemultiple[]" multiple data-allow-reorder="true" hidden>
                                        <ion-icon name="cloud-upload-outline" class="upload-icon-absolute"></ion-icon>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="grn-notes">
                                            <h4 class="text-xs">Note:</h4>
                                            <hr>
                                            <ul class="pl-0 mb-0">
                                                <li>
                                                    <p class="text-xs">You can uplod multiple bill/s at a time</p>
                                                </li>
                                                <li>
                                                    <p class="text-xs">Your maximum file size should be <span class="font-bold text-xs">2 mb/file</span></p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <div id="robo_icon_static" class="grn-robot-icon">
                                    <button type="submit" class="upload-button btn btn-sm bg-transparent" id="uploadGrnInvoiceFileFormSubmitBtn">
                                        <img src="../../public/assets/img/CLick-Me.webp" width="200" alt="">
                                    </button>
                                </div>
                                <div id="robo_text" class="speech-bubble swing invisible">
                                    <p class="font-bold mb-0" id="main_robo_text"></p>
                                </div>
                                <div id="robo_icon">
                                    <input type="submit" class="upload-button btn btn-primary" value="Upload" />
                                </div>
                            </div>

                        </div>
                    </form>

                    <div id="grnUploadCard">
                    </div>

                    <!-- Robot Div End -->
                    <div class="row create-grn-step">
                        <div id="uploadGrnInvoiceDiv" class="create-grn">
                            <form id="uploadGrnInvoiceFileForm" action="" method="post" enctype="multipart/form-data">
                                <div class="upload-files-container">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="head">
                                                <h4>Upload Invoice</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="drag-file-area">
                                                <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                                <br>
                                                <input type="file" class="form-control" id="invoiceFileInput" name="grnInvoiceFile" placeholder="Invoice Upload" required />
                                            </div>
                                            <div class="file-block">
                                                <div class="progress-bar"> </div>
                                            </div>
                                            <button type="submit" class="upload-button btn btn-primary" id="uploadGrnInvoiceFileFormSubmitBtn"> Upload </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- <span class="text-light">Select Invoice</span>
                        <input type="file" id="invoiceFileInput" name="grnInvoiceFile" class="form-control p-2 pl-3" placeholder="Invoice Upload" required />
                        <button type="submit" class="form-control mt-1" id="uploadGrnInvoiceFileFormSubmitBtn">Upload</button> -->
                            </form>
                        </div>
                        <div id="processingGrnInvoiceDiv" class="create-grn processing-grn">
                            <div class="card">
                                <!-- <div class="card-body">
                            <p>Please wait, while we are processing your invoice!</p>
                            <img src="../../public/assets/animated-icon/grn-robot.gif" class="ocr-reading-animation">
                            <p class="text-center text-muted"> <i class="fa fa-clock" style="color:#003060"></i> <span id="orcTimer" class="text-bold" style="color:#003060">00 s</span></p>
                        </div> -->
                            </div>

                        </div>

                        <div id="goBackBtn">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item" style="visibility: hidden;"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                                <li class="breadcrumb-item active" style="visibility: hidden;"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                                <li class="breadcrumb-item active" style="visibility: hidden;"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                                    </a></li>

                                <li class="back-button">
                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                                        <i class="fa fa-reply po-list-icon"></i>
                                    </a>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if (isset($_POST["vendorCode"]) && $_POST["vendorCode"] != "") {

                            $grnObj = new GrnController();
                            $createGrnObj = $grnObj->createGrn($_POST);
                            console($createGrnObj);
                            // exit;
                            // if ($createGrnObj["status"] == "success") {

                            //     swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"], BASE_URL . "branch/location/manage-grn.php");
                            // } else {
                            //     swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"]);
                            // }

                            //console("Hello grn process");
                            //console($createGrnObj);

                        }
                        ?>
                    </div>
                    <div class="row" id="invoiceProcessedForm" style="overflow-y:auto;">
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="head">
                        <div class="title">
                            <h4 class="head-title text-sm mb-0"></h4>
                        </div>

                        <div class="search-filter d-flex mr-3">
                            <div class="section serach-input-section d-flex gap-2 mr-3">
                                <input type="date" class="dataTables_filter form-control" id="from_date" placeholder="search.." class="field serachfilter-hsn form-control">
                                <!-- <button type="button" class="btn-close" id="clearButton" data-bs-dismiss="modal" aria-label="Close"></button> -->

                                <input type="date" class="dataTables_filter form-control" id="to_date" placeholder="search.." class="field serachfilter-hsn form-control">
                                <!-- <button type="button" class="btn-close" id="clearButton" data-bs-dismiss="modal" aria-label="Close"></button> -->

                                <input type="text" class="dataTables_filter form-control" id="searchbar" placeholder="search.." class="field serachfilter-hsn form-control">
                                <!-- <button type="button" class="btn-close" id="clearButton" data-bs-dismiss="modal" aria-label="Close"></button> -->

                            </div>
                            <input type="button" class="btn btn-primary preview-btn grn-po-post waves-effect waves-light" id="postdate" value="POST">
                        </div>

                    </div>

                    <form id="po_form">
                        <div class="body-table">
                            <table class="table-sales-order table defaultDataTable grn-table">
                                <thead>
                                    <tr>
                                        <th>PO Number</th>
                                        <th>PO Date</th>
                                        <th>Reference Number</th>
                                        <th>Vendor Name</th>
                                        <th>Vendor Code</th>
                                        <th>PO Types</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="po_tbody">

                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            <!-- <div class="container select-grn-type">
                <label for="" class="text-xs font-bold">Select GRN type:</label>
                <hr class="mt-2 mb-0">
                <div class="grn-type">
                    <div class="auto-grn">
                        <input type="radio" id="auto" name="type" value="auto" checked>
                        <label for="auto" class="mb-0">Automatic GRN</label>
                    </div>
                    <div class="po-grn">
                        <input type="radio" id="po" name="type" value="po">
                        <label for="po" class="mb-0">GRN through PO</label>
                    </div>
                </div>
            </div> -->


            <!-- <div class="modal fade grn-invoice-modal" id="myModalPO" tabindex="-1" aria-labelledby="exampleModal2Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <div class="title">
                                <h4 class="head-title text-sm mb-0">Select Type</h4>
                            </div>
                            <div class="search-filter">
                                <div class="section serach-input-section">
                                    <input type="text" class="dataTables_filter form-control" id="searchbar" placeholder="" class="field serachfilter-hsn form-control">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <?php
                        $po_sql = "SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE  erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "' AND erp_branch_purchase_order.`po_status`=9 ORDER BY erp_branch_purchase_order.`po_id` DESC ";
                        $po_get = queryGet($po_sql, true);
                        $po_data = $po_get['data'];
                        ?>
                        <div class="modal-body">
                            <form id="po_form">
                                <div class="modal-body" style="height: 500px;">
                                    <table class="table-sales-order table defaultDataTable grn-table">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th>PO Number</th>
                                                <th>PO Date</th>
                                                <th>Reference Number</th>
                                                <th>Vendor Name</th>
                                                <th>Vendor Code</th>
                                                <th>PO Types</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="po_tbody">
                                            <?php foreach ($po_data as $onePoList) {
                                                $rand = rand(10, 1000);
                                            ?>
                                                <tr>
                                                    <td><input type="radio" name="po-creation" value="<?= $onePoList['po_number'] ?>" id="po_number" class="form poId"></td>
                                                    <td><?= $onePoList['po_number'] ?></td>
                                                    <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
                                                    <td><?= $onePoList['ref_no'] ?></td>
                                                    <td><?= $onePoList['trade_name'] ?></td>
                                                    <td><?= $onePoList['vendor_code'] ?></td>
                                                    <td><?= $onePoList['use_type'] ?></td>
                                                    <td>₹<?= $onePoList['totalAmount'] ?? 0.00 ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary grnbtn" id="grn_button" value="grn">GRN</button>
                                    <button type="button" class="btn btn-primary grnbtn" value="srn" id="srn_button">SRN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> -->


            <div class="modal fade hsn-dropdown-modal" id="myModalManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Select Type
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="row hsn-add">
                                    <div class="col-md-6 col">
                                        <a href="<?= LOCATION_URL; ?>manage-manual-grn.php?view=nopo&type=grn"><button type="button" class="btn btn-primary">GRN</button></a>
                                    </div>
                                    <div class="col-md-6 col">
                                        <a href="<?= LOCATION_URL; ?>manage-manual-grn.php?view=nopo&type=srn"><button type="button" class="btn btn-primary">SRN</button></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <script>
                $('input:radio[name="type"]').change(
                    function() {
                        if ($(this).is(':checked') && $(this).val() == 'po') {
                            $('#myModalPO').modal('show');
                        } else if ($(this).is(':checked') && $(this).val() == 'manual') {
                            $('#myModalManual').modal('show');
                        } else {
                            $('#myModalPO').modal('hide');
                            $('#myModalManual').modal('hide');
                        }
                    });

                $(".grnbtn").click(function() {
                    var po = $('input[name=po-creation]:checked', '#po_form').val();
                    if (po == "") {
                        alert("PO value is needed");
                    } else {
                        var value = $(this).val();
                        if (value == "grn") {
                            var url = `<?= LOCATION_URL ?>manage-manual-grn.php?view=${po}&type=grn`;
                        } else {
                            var url = `<?= LOCATION_URL ?>manage-manual-grn.php?view=${po}&type=srn`;
                        }

                        // alert(url);
                        window.location = url;
                    }
                });
            </script>


            <!-- modal -->
            <div class="modal" id="mapInvoiceItemCode">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h5 class="modal-title">Map Item</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" name="itemName" class="m-input" required>
                                    <label>Item Name</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="itemDesc" class="m-input" required>
                                    <label>Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal end -->
        </div>
    </section>
</div>

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        var searchBar = document.getElementById('searchbar');
        var clearButton = document.getElementById('clearButton');

        clearButton.addEventListener('click', function() {
            searchBar.value = '';
        });
    });
</script> -->

<script>
    function loadpo(pageNo, limit, keyword = null, from_date = null, to_date = null) {
        $.ajax({
            method: 'POST',
            data: {
                pageNo: pageNo,
                limit: limit,
                keyword: keyword,
                form_date_s: from_date,
                to_date_s: to_date
            },
            url: `ajaxs/grn/ajax-po.php`,
            beforeSend: function() {
                $(".hsnSearchSpinner").show();
                $(".po_tbody").html('<tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loading ...</td></tr>');
            },
            success: function(data) {
                // var response = JSON.parse(data);
                $(".po_tbody").html(data);
                $(".hsnSearchSpinner").hide();

            }

        });

    }

    loadpo(0, 50);

    $(document).ready(function() {
        $('#grnUploadCard').hide();
        $("#processingGrnInvoiceDiv").hide();
        $("#goBackBtn").hide();

        $(".hsnSearchSpinner").hide();
        // $('#searchbar').on('keyup keydown paste', function() {
        //     var keyword = $("#searchbar").val();
        //     var from_date = $("#from_date").val();
        //     var to_date = $("#to_date").val();
        //     if(keyword!=''){
        //     var pageNo = 0;
        //     var limit = 50;
        //     setTimeout(function() {
        //         loadpo(pageNo, limit, keyword, from_date, to_date);
        //     },1000);
        // }

        // });

        $('#postdate').on('click', function() {
            // alert("abc");
            var keyword = $("#searchbar").val();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();

            if (new Date(to_date) < new Date(from_date)) {
                alert("To date cannot be greater than from date!");
            } else {
                if (to_date == '' && from_date != '') {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is zero-based
                    const day = String(today.getDate()).padStart(2, '0');

                    const currentDate = `${year}-${month}-${day}`;
                    console.log(currentDate);


                    to_date = currentDate;
                } else if (from_date == '' && to_date != '') {
                    alert("Please give the from date and then filter!");
                }
                var pageNo = 0;
                var limit = 100;
                loadpo(pageNo, limit, keyword, from_date, to_date);
            }


        });


        $("#goBackBtn").click(function() {
            window.location.reload();
        });

        FilePond.registerPlugin(

            // encodes the file as base64 data
            FilePondPluginFileEncode,

            // validates the size of the file
            FilePondPluginFileValidateSize,

            // corrects mobile image orientation
            FilePondPluginImageExifOrientation,

            // previews dropped images
            FilePondPluginImagePreview
        );

        pond = FilePond.create(
            document.querySelector('#upload_grn_input'), {
                allowMultiple: true,
                instantUpload: false,
                allowProcess: false,
                credits: false
            });

        pond.on('addfile', (error, file) => {
            pondFileAddRemoveEvent();
        });

        pond.on('removefile', (error, file) => {
            pondFileAddRemoveEvent();
        });

        function pondFileAddRemoveEvent() {
            pondFiles = pond.getFiles();
            var pondfilLength = pondFiles.length;
            if (pondfilLength > 0) {
                $("#robo_text").removeClass('invisible');
                $("#main_robo_text").text('Hit! me to execute');
                $('.row.grn-box').css('border', 'none');
            } else {
                $("#main_robo_text").text('Choose file first!');
            }
        }


        $("#upload_form").submit(function(e) {
            e.preventDefault();
            var fd = new FormData(this);
            // append files array into the form data
            pondFiles = pond.getFiles();
            var pondfilLength = pondFiles.length;

            if (pondfilLength < 1) {
                $("#robo_text").removeClass('invisible');
                $("#main_robo_text").text('Choose file first!');
                return false;
            } else {
                showDiv();
                $("#robo_text").addClass('invisible');
                $('#robo_icon_static').html('<img class="ocr-reading-animation" src="../../public/assets/gif/OCR-2.gif" width="200" alt="">');


                for (var i = 0; i < pondFiles.length; i++) {
                    fd.append('grnInvoiceFilemultiple[]', pondFiles[i].file);
                }
                console.log(fd.length);

                $.ajax({
                    url: 'ajaxs/grn/ajax-multiple-invoice-grn.php',
                    type: "POST",
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        console.log("uploading.........");
                    },
                    success: function(data) {
                        $('#upload_form').hide();
                        hideDiv();
                        var response = JSON.parse(data);
                        $('#grnUploadCard').html(response["data"]);
                        $('.ocrLimits').html(<?= $_SESSION['ocrLimits']; ?>);
                        $('#grnUploadCard').show();

                        console.log(response["data"]);

                        if (response["link"] != "") {
                            window.location.replace(response["link"]);
                        }
                    },
                    error: function(data) {
                        //    todo the logic
                    }
                });
            }
        });


        $(document).on("click", ".grn_proceed", function() {

            var concatenatedValue = '';
            var id = $(this).data('id');
            var branch_gst = $(this).data('branch');
            var inv_no = $(this).data('inv_no');
            var po_no = $(this).data('po_no');
            var gst_no = $(this).data('gst_no');
            var vendor_name = $(this).data('vendor_name');
            var CustomerName = $(this).data('Customername');
            var grn_read_json = $(this).data('grn_read_json');
            var original_file_name = $(this).data('original_file_name');
            var uploaded_file_name = $(this).data('uploaded_file_name');
            var total_amt = $(this).data('total_amt');
            var vendorPan = $(this).data('vendorpan');
            $('.gst_' + id).each(function() {
                var value = $(this).html();
                concatenatedValue += value;
            });
            // alert(branch_gst);

            if (concatenatedValue == branch_gst) {
                $.ajax({
                    url: 'ajaxs/grn/ajax-grn-proceed.php',
                    type: 'POST',
                    data: {
                        branch_gst,
                        inv_no,
                        po_no,
                        gst_no,
                        vendor_name,
                        CustomerName,
                        grn_read_json,
                        original_file_name,
                        uploaded_file_name,
                        total_amt,
                        vendorPan,
                        concatenatedValue
                    },
                    beforeSend: function() {
                        $(this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(responseData) {
                        responseObj = JSON.parse(responseData);
                        console.log(responseData);
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: `${responseObj['status']}`,
                            title: `&nbsp;${responseObj['message']}`
                        });
                    }
                });
            } else {
                alert('GSTIN Not Matched');
            }
        });



        $(document).on('keyup keydown', ".gst_input_change", function(e) {

            // alert("keyup");
            var id = $(this).data('id');

            var value1 = $(this).html();

            var value2 = $('.gst_col_' + id + '_2').html();

            console.log(value1);
            console.log(value2);
            console.log(".gst_col_" + id + "_2");

            if (value1 == value2) {
                $('.gst_col_' + id + '_1').css("background-color", "");
                $('.gst_col_' + id + '_2').css("background-color", "");
            } else {
                $('.gst_col_' + id + '_1').css("background-color", "Olive");
                $('.gst_col_' + id + '_2').css("background-color", "Olive");
            }

        });

    });
</script>
<script>
    function showDiv() {
        document.getElementById('showme').style.display = "flex";
        document.getElementsByClassName('blur')[0].style.filter = "blur(8px)";
    }

    function hideDiv() {
        document.getElementById('showme').style.display = "none";
        document.getElementsByClassName('blur')[0].style.filter = "";
    }
</script>
<script>
    var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 800;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">' + this.txt + '</span>';

        var that = this;
        var delta = 200 - Math.random() * 100;

        if (this.isDeleting) {
            delta /= 2;
        }

        if (!this.isDeleting && this.txt === fullTxt) {
            delta = this.period;
            this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
            this.isDeleting = false;
            this.loopNum++;
            delta = 500;
        }

        setTimeout(function() {
            that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i = 0; i < elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
                new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
        // INJECT CSS
        var css = document.createElement("style");
        css.type = "text/css";
        css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #003060}";
        document.body.appendChild(css);
    };
</script>
<?php
require_once("../common/footer.php");
?>