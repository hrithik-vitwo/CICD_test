<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
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



<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
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
    .vitwo-alpha-global .dataTables_wrapper {
        overflow: auto;
        height: calc(100vh - 308px);

    }

    .report-wrapper .reports-card {
        background: #fff;
        margin-top: 0px;
    }


    .head-state-table.row {
        width: 100%;
        display: flex;
    }

    /* Styles for the header row children */
    .head-state-table.row>.col-lg-2 {
        flex-grow: 1;
        width: auto;
        flex-basis: 0;
    }

    /* Styles for the body row */
    .row.body-state-table {
        width: 100%;
        display: flex;
    }

    /* Styles for the body row children */
    .row.body-state-table>.col-lg-2 {
        flex-grow: 1;
        width: auto;
        flex-basis: 0;
    }
</style>


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

<!-- css for making trail content scroll  -->

<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>




<div class="loading-image" id="showme" style="display: none;">
    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_ofa3xwo7.json" class="ocr-reading-animation"
        background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay>

    </lottie-player>

    <h1>
        <a href="" class="typewrite" data-period="800"
            data-type='[ "Relax for a while... !", "Back to your seat for few minutes.....", "I will handle it" ]'>
            <span class="wrap"></span>
        </a>
    </h1>
</div>

<?php
if (!isset($_SESSION['ocr_limitation'])) {
    $_SESSION['ocr_limitation'] = 200;
}
$ocrLimits = $_SESSION['ocr_limitation'];

$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;

if (!isset($_COOKIE["cookiegrnInvoice"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiegrnInvoice", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'PO Number',
        'slag' => 'po_number',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'PO Date',
        'slag' => 'po_date',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'Reference Number',
        'slag' => 'ref_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'trade_name',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Code',
        'slag' => 'vendor_code',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'PO Types',
        'slag' => 'use_type',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Total Amount',
        'slag' => 'totalAmount',
        'icon' => '',
        'dataType' => 'number'
    ]
];


?>

<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global is-grn-invoice blur">
    <section class="content">
        <div class="container-fluid">

            <ul class="nav nav-pills select-grn-type mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                        aria-selected="true">
                        <div class="auto-grn grn-tab">
                            <!-- <input type="radio" id="auto" name="type" value="auto" checked> -->
                            <label for="auto" class="mb-0">Automatic GRN</label>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                        aria-selected="false">
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
                        <p class="text-xs mb-0">Your available limit balance <span
                                class="font-bold text-sm ocrLimits"><?php echo $ocrLimits; ?></span> doc.</p>
                    </div>
                    <!-- Robot Div Start -->
                    <form id="upload_form" method="POST" enctype="multipart/form-data">
                        <div class="row grnCreate">
                            <div class="col-lg-8 col-md-12 col-sm-12">
                                <div class="row box grn-box">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <p class="font-bold text-xs heading-line">Multiple Upload</p>
                                        <input id="upload_grn_input" type="file" class="filepond"
                                            name="grnInvoiceFilemultiple[]" multiple data-allow-reorder="true">
                                        <input id="upload_grn_input" type="file" class="filepond"
                                            name="grnInvoiceFilemultiple[]" multiple data-allow-reorder="true" hidden>
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
                                                    <p class="text-xs">Your maximum file size should be <span
                                                            class="font-bold text-xs">2 mb/file</span></p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12">
                                <div id="robo_icon_static" class="grn-robot-icon">
                                    <button type="submit" class="upload-button btn btn-sm bg-transparent"
                                        id="uploadGrnInvoiceFileFormSubmitBtn">
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
                                                <input type="file" class="form-control" id="invoiceFileInput"
                                                    name="grnInvoiceFile" placeholder="Invoice Upload" required />
                                            </div>
                                            <div class="file-block">
                                                <div class="progress-bar"> </div>
                                            </div>
                                            <button type="submit" class="upload-button btn btn-primary"
                                                id="uploadGrnInvoiceFileFormSubmitBtn"> Upload </button>
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
                                <li class="breadcrumb-item" style="visibility: hidden;"><a href="<?= BRANCH_URL ?>"
                                        class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                                <li class="breadcrumb-item active" style="visibility: hidden;"><a
                                        href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                                            class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                                <li class="breadcrumb-item active" style="visibility: hidden;"><a href=""
                                        class="text-dark"><i class="fa fa-plus po-list-icon"></i>
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


                    <!-- <form id="po_form"> -->
                    <div class="body-table">
                        <div class="row p-0 m-0">
                            <div class="col-12 p-0">
                                <div class="card card-tabs reports-card">
                                    <div class="card-body">
                                        <div class="row filter-serach-row m-0">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row table-header-item">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                            <!---------------------- Search START -->
                                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab"
                                                role="tablist">
                                                <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                                    style="width:100%">
                                                    <div class="left-block">
                                                        <div class="label-select">
                                                            <h3 class="card-title mb-0">Manage GRN Invoice
                                                            </h3>
                                                        </div>
                                                    </div>


                                                    <div class="right-block">

                                                        <button class="btn btn-sm fillscreen-btn"
                                                            onclick="openFullscreen()">
                                                            <ion-icon name="expand-outline"></ion-icon>
                                                        </button>
                                                        <button type="button" id="revealList" class="page-list">
                                                            <ion-icon name="funnel-outline"></ion-icon>
                                                        </button>
                                                        <div id="modal-container">
                                                            <div class="modal-background">
                                                                <div class="modal">
                                                                    <button class="btn-close-modal"
                                                                        is="closeFilterModal">
                                                                        <ion-icon name="close-outline"></ion-icon>
                                                                    </button>
                                                                    <h5>Filter Pages</h5>

                                                                    <h5>Search and Export</h5>
                                                                    <div
                                                                        class="filter-action filter-mobile-search mobile-page">
                                                                        <a type="button"
                                                                            class="btn add-col setting-menu"
                                                                            data-toggle="modal" data-target="#myModal1">
                                                                            <ion-icon
                                                                                name="settings-outline"></ion-icon></a>
                                                                        <div class="filter-search">
                                                                            <div class="icon-search" data-toggle="modal"
                                                                                data-target="#btnSearchCollpase_modal">
                                                                                <ion-icon
                                                                                    name="filter-outline"></ion-icon>
                                                                                Advance Filter
                                                                            </div>
                                                                        </div>

                                                                        <!-- <a href=""
                                                                                class="btn btn-create mobile-page mobile-create additemdiscountMrpbtn"
                                                                                data-toggle="modal"
                                                                                data-target="#funcAddForm"
                                                                                type="button">
                                                                                <ion-icon name="add-outline"></ion-icon>
                                                                                Create
                                                                            </a> -->

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <!---------------------- Search END -->
                                        </div>



                                        <div class="card card-tabs mobile-transform-card mb-0"
                                            style="border-radius: 20px;">
                                            <div class="card-body">
                                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                                    <div class="tab-pane dataTableTemplate dataTable_stock fade show active"
                                                        id="listTabPan" role="tabpanel" aria-labelledby="listTab"
                                                        style="background: #fff; border-radius: 20px;">
                                                        <div class="length-row mobile-legth-row">
                                                            <span>Show</span>
                                                            <select name="" id="" class="custom-select" value="25">
                                                                <option value="10">10</option>
                                                                <option value="25" selected="selected">25</option>
                                                                <option value="50">50</option>
                                                                <option value="100">100</option>
                                                                <option value="200">200</option>
                                                                <option value="250">250</option>
                                                            </select>
                                                            <span>Entries</span>
                                                        </div>
                                                        <div class="filter-action">
                                                            <a type="button" class="btn add-col setting-menu"
                                                                data-toggle="modal" data-target="#myModal1">
                                                                <ion-icon name="settings-outline"></ion-icon> Manage
                                                                Column</a>
                                                            <div class="length-row">
                                                                <span>Show</span>
                                                                <select name="" id="grnInvoiceLimit"
                                                                    class="custom-select">
                                                                    <option value="10">10</option>
                                                                    <option value="25" selected="selected">25
                                                                    </option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                                <span>Entries</span>
                                                            </div>
                                                            <div class="filter-search">
                                                                <div class="icon-search" data-toggle="modal"
                                                                    data-target="#btnSearchCollpase_modal">
                                                                    <p>Advance Search</p>
                                                                    <ion-icon name="filter-outline"></ion-icon>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="exportgroup">
                                                            <button class="exceltype btn btn-primary btn-export" type="button">
                                                                <ion-icon name="download-outline"></ion-icon>
                                                                Export
                                                            </button>
                                                            <ul class="export-options">
                                                                <li>
                                                                    <button class="ion-paginationlistnew">
                                                                        <ion-icon name="list-outline" class="ion-paginationlistnew md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                                    </button>
                                                                </li>
                                                                <li>

                                                                    <button class="ion-fulllistnew">
                                                                        <ion-icon name="list-outline" class="ion-fulllistnew md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>


                                                        <!-- <a href=""
                                                                class="btn btn-create mobile-page mobile-create addMrpbtn"
                                                                data-toggle="modal" data-target="#funcAddForm"
                                                                type="button">
                                                                <ion-icon name="add-outline"></ion-icon>
                                                                Create
                                                            </a> -->


                                                        <table id="dataTable_detailed_view"
                                                            class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                            <thead>
                                                                <tr>
                                                                    <?php
                                                                    foreach ($columnMapping as $index => $column) {
                                                                    ?>
                                                                        <th data-value="<?= $index ?>">
                                                                            <?= $column['name'] ?>
                                                                        </th>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="detailed_tbody">
                                                            </tbody>
                                                        </table>
                                                        <div class="row custom-table-footer">
                                                            <div class="col-lg-6 col-md-6 col-12">
                                                                <div id="limitText" class="limit-text">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-12">
                                                                <div id="yourDataTable_paginate">
                                                                    <div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!---------------------------------deialed View Table settings Model Start--------------------------------->
                                                        <div class="modal manage-column-setting-modal" id="myModal1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title text-sm">Detailed
                                                                            View Column
                                                                            Settings</h4>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <form name="table_settings_detailed_view"
                                                                        method="POST"
                                                                        action="<?php $_SERVER['PHP_SELF']; ?>">
                                                                        <div class="modal-body"
                                                                            style="max-height: 450px;">
                                                                            <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                            <input type="hidden" id="tablename"
                                                                                name="tablename"
                                                                                value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                            <input type="hidden" id="pageTableName"
                                                                                name="pageTableName"
                                                                                value="ERP_TEST_<?= $pageName ?>" />
                                                                            <div class="modal-body">
                                                                                <div id="dropdownframe"></div>
                                                                                <div id="main2">
                                                                                    <div
                                                                                        class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                                        <input type="checkbox"
                                                                                            class="grand-checkbox"
                                                                                            value="" />
                                                                                        <p class="text-xs font-bold">
                                                                                            Check All</p>
                                                                                    </div>

                                                                                    <table class="colomnTable">
                                                                                        <?php
                                                                                        $cookieTableStockReport = json_decode($_COOKIE["cookieDiscountVariant"], true) ?? [];

                                                                                        foreach ($columnMapping as $index => $column) {

                                                                                        ?>
                                                                                            <tr>
                                                                                                <td valign="top">

                                                                                                    <input type="checkbox"
                                                                                                        class="settingsCheckbox_detailed"
                                                                                                        name="settingsCheckbox[]"
                                                                                                        id="settingsCheckbox_detailed_view[]"
                                                                                                        value='<?= $column['slag'] ?>'>
                                                                                                    <?= $column['name'] ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php
                                                                                        }
                                                                                        ?>

                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="submit" id="check-box-submt"
                                                                                name="check-box-submit"
                                                                                data-dismiss="modal"
                                                                                class="btn btn-primary">Save</button>
                                                                            <button type="button" class="btn btn-danger"
                                                                                data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!---------------------------------Table Model End--------------------------------->

                                                        <div class="modal " id="btnSearchCollpase_modal" tabindex="-1"
                                                            role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-sm"
                                                                            id="exampleModalLongTitle">
                                                                            Advanced Filter</h5>
                                                                    </div>
                                                                    <form id="myForm" method="post" action="">
                                                                        <div class="modal-body">

                                                                            <table>
                                                                                <tbody>
                                                                                    <?php
                                                                                    $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                                    foreach ($columnMapping as $columnIndex => $column) {
                                                                                        if ($columnIndex === 0) {
                                                                                            continue;
                                                                                        } ?>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div
                                                                                                    class="icon-filter d-flex align-items-center gap-2">
                                                                                                    <?= $column['icon'] ?>
                                                                                                    <p
                                                                                                        id="columnName_<?= $columnIndex ?>">
                                                                                                        <?= $column['name'] ?>
                                                                                                    </p>
                                                                                                    <input type="hidden"
                                                                                                        id="columnSlag_<?= $columnIndex ?>"
                                                                                                        value="<?= $column['slag'] ?>">
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <select
                                                                                                    class="form-control selectOperator"
                                                                                                    id="selectOperator_<?= $columnIndex ?>"
                                                                                                    name="operator[]"
                                                                                                    val="">
                                                                                                    <?php
                                                                                                    if (($column['dataType'] === 'date')) {
                                                                                                        $operator = array_slice($operators, -3, 3);
                                                                                                        foreach ($operator as $oper) {
                                                                                                    ?>
                                                                                                            <option
                                                                                                                value="<?= $oper ?>">
                                                                                                                <?= $oper ?>
                                                                                                            </option>
                                                                                                        <?php
                                                                                                        }
                                                                                                    } elseif ($column['dataType'] === 'number') {
                                                                                                        $operator = array_slice($operators, 2, 6);
                                                                                                        foreach ($operator as $oper) {
                                                                                                        ?>
                                                                                                            <option
                                                                                                                value="<?= $oper ?>">
                                                                                                                <?= $oper ?>
                                                                                                            </option>
                                                                                                            <?php

                                                                                                        }
                                                                                                    } else {
                                                                                                        $operator = array_slice($operators, 0, 2);
                                                                                                        foreach ($operator as $oper) {
                                                                                                            if ($oper === 'CONTAINS') {
                                                                                                            ?>
                                                                                                                <option value="LIKE">
                                                                                                                    <?= $oper ?>
                                                                                                                </option>
                                                                                                            <?php
                                                                                                            } else { ?>

                                                                                                                <option
                                                                                                                    value="NOT LIKE">
                                                                                                                    <?= $oper ?>
                                                                                                                </option>

                                                                                                    <?php
                                                                                                            }
                                                                                                        }
                                                                                                    } ?>
                                                                                                </select>
                                                                                            </td>
                                                                                            <td id="td_<?= $columnIndex ?>">
                                                                                                <input
                                                                                                    type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>"
                                                                                                    data-operator-val=""
                                                                                                    name="value[]"
                                                                                                    class="fld form-control m-input"
                                                                                                    id="value_<?= $columnIndex ?>"
                                                                                                    placeholder="Enter Keyword"
                                                                                                    value="">
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" id="serach_reset"
                                                                                class="btn btn-primary">Reset</button>
                                                                            <button type="submit" id="serach_submit"
                                                                                class="btn btn-primary"
                                                                                data-dismiss="modal">Search</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <!-- Global View start-->

                                                        <div class="modal right fade global-view-modal"
                                                            id="viewGlobalModal" role="dialog"
                                                            aria-labelledby="myModalLabel" data-backdrop="true"
                                                            aria-modal="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <div class="top-details">
                                                                            <div class="left">
                                                                                <p class="info-detail amount"
                                                                                    id="totalAmount">
                                                                                    <ion-icon name="wallet-outline"
                                                                                        role="img" class="md hydrated"
                                                                                        aria-label="wallet outline"></ion-icon>

                                                                                </p>

                                                                                <p class="info-detail po-number">
                                                                                    <ion-icon name="information-outline"
                                                                                        role="img" class="md hydrated"
                                                                                        aria-label="information outline"></ion-icon><span
                                                                                        id="po_number"></span>
                                                                                </p>

                                                                                <p class="info-detail po-number">
                                                                                    <ion-icon name="information-outline"
                                                                                        role="img" class="md hydrated"
                                                                                        aria-label="information outline"></ion-icon><span
                                                                                        id="ref_no"></span>
                                                                                </p>

                                                                            </div>
                                                                            <div class="right">
                                                                                <p class="info-detail name">
                                                                                    <ion-icon name="business-outline"
                                                                                        role="img" class="md hydrated"
                                                                                        aria-label="business outline"></ion-icon><span
                                                                                        id="trade_name"></span>
                                                                                </p>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <nav>
                                                                            <div class="nav nav-tabs global-view-navTabs"
                                                                                id="nav-tab" role="tablist">
                                                                                <button
                                                                                    class="nav-link ViewfirstTab active"
                                                                                    id="nav-overview-tab"
                                                                                    data-bs-toggle="tab"
                                                                                    data-bs-target="#nav-overview"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="nav-overview"
                                                                                    aria-selected="true"><ion-icon
                                                                                        name="apps-outline" role="img"
                                                                                        class="md hydrated"
                                                                                        aria-label="apps outline"></ion-icon>Info</button>

                                                                                <button class="nav-link auditTrail"
                                                                                    id="nav-trail-tab"
                                                                                    data-bs-toggle="tab"
                                                                                    data-bs-target="#nav-trail"
                                                                                    data-ccode="" type="button"
                                                                                    role="tab" aria-controls="nav-trail"
                                                                                    aria-selected="false"
                                                                                    tabindex="-1"><ion-icon
                                                                                        name="time-outline" role="img"
                                                                                        class="md hydrated"
                                                                                        aria-label="time outline"></ion-icon>Trail</button>
                                                                            </div>
                                                                        </nav>
                                                                        <div class="tab-content global-tab-content"
                                                                            id="nav-tabContent">

                                                                            <div class="tab-pane fade transactional-data-tabpane active show"
                                                                                id="nav-overview" role="tabpanel"
                                                                                aria-labelledby="nav-overview-tab">
                                                                                <div class="d-flex nav-overview-tabs">
                                                                                    <div class="action-btns display-flex-gap create-delivery-btn-sales"
                                                                                        id="action-navbar">
                                                                                        <div class="d-flex btnHideShow">
                                                                                            <a id="grnLink" href=""
                                                                                                class="btn btn-primary "><i
                                                                                                    class="fa fa-plus mr-2"></i>GRN
                                                                                            </a>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div
                                                                                        class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                                                        <div class="items-table">
                                                                                            <h4>Vendor Details
                                                                                            </h4>
                                                                                            <div
                                                                                                class="customer-details">
                                                                                                <div class="name-code">
                                                                                                    <div
                                                                                                        class="details name">
                                                                                                        <p
                                                                                                            id="vendor_name">

                                                                                                        </p>
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="details code">
                                                                                                        <p
                                                                                                            id="vendor_code">

                                                                                                        </p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div
                                                                                                    class="details gstin">
                                                                                                    <label
                                                                                                        for="">GSTIN</label>
                                                                                                    <p id="vendor_gstin"
                                                                                                        title="">
                                                                                                    </p>
                                                                                                </div>


                                                                                            </div>
                                                                                        </div>


                                                                                    </div>


                                                                                </div>

                                                                                <div class="row orders-table">
                                                                                    <div
                                                                                        class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                        <div class="items-table">
                                                                                            <h4>Item Details</h4>
                                                                                            <div
                                                                                                class="multiple-item-table">
                                                                                                <div
                                                                                                    class="row head-state-table">
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Code</div>
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Name</div>

                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Unit Price
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Qty</div>
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Remaining
                                                                                                        Qty</div>
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                        Total Price
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td ">
                                                                                                        Delivery
                                                                                                        Date
                                                                                                    </div>

                                                                                                </div>
                                                                                                <div id="itemTableBody">

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>


                                                                            </div>

                                                                            <div class="tab-pane fade" id="nav-trail"
                                                                                role="tabpanel"
                                                                                aria-labelledby="nav-trail-tab">
                                                                                <div class="inner-content">
                                                                                    <div
                                                                                        class="audit-head-section mb-3 mt-3 ">
                                                                                        <p class="text-xs font-italic">
                                                                                            <span
                                                                                                class="font-bold text-normal">Created
                                                                                                by </span><span
                                                                                                class="created_by_trail"></span>
                                                                                        </p>
                                                                                        <p class="text-xs font-italic">
                                                                                            <span
                                                                                                class="font-bold text-normal">Last
                                                                                                Updated
                                                                                                by </span><span
                                                                                                class="updated_by">
                                                                                            </span>
                                                                                        </p>
                                                                                    </div>
                                                                                    <hr>
                                                                                    <div
                                                                                        class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                                                                    </div>
                                                                                    <div class="modal fade right audit-history-modal"
                                                                                        id="innerModal" role="dialog"
                                                                                        aria-labelledby="innerModalLabel"
                                                                                        aria-modal="true">
                                                                                        <div class="modal-dialog">
                                                                                            <div
                                                                                                class="modal-content auditTrailBodyContentLineDiv">

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer"></div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <!-- Global View end -->

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- </form> -->
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


            <div class="modal fade hsn-dropdown-modal" id="myModalManual" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Select Type
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="row hsn-add">
                                    <div class="col-md-6 col">
                                        <a href="<?= LOCATION_URL; ?>manage-manual-grn.php?view=nopo&type=grn"><button
                                                type="button" class="btn btn-primary">GRN</button></a>
                                    </div>
                                    <div class="col-md-6 col">
                                        <a href="<?= LOCATION_URL; ?>manage-manual-grn.php?view=nopo&type=srn"><button
                                                type="button" class="btn btn-primary">SRN</button></a>
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
require_once("../common/footer2.php");
?>


<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });
</script>

<script>
    // $('.btn-edit').on('click', function () {
    //     let gid = $(this).data('gid');

    //     let gname = $(this).data('gname');

    //     $('#editmrpGroupName').val(gname);
    //     $('#editGroupId').val(gid);


    //     $("#editFunctionality").modal('show');

    // });






    $('.m-input').on('keyup', function() {
        $(this).parent().children('.error').hide()
    });
    /*
      $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#functionalities_name").val() == "") {
          $(".functionalities_name").show();
          $(".functionalities_name").html("functionalities name is requried.");
          flag++;
        } else {
          $(".functionalities_name").hide();
          $(".functionalities_name").html("");
        }
        if ($("#functionalities_desc").val() == "") {
          $(".functionalities_desc").show();
          $(".functionalities_desc").html("Description is requried.");
          flag++;
        } else {
          $(".functionalities_desc").hide();
          $(".functionalities_desc").html("");
        }
        if (flag == 1) {
          $("#add_frm").submit();
        }


      });
      $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
      });
    */

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




<!-----------mobile filter list------------>


<script>
    $(document).ready(function() {
        $("button.page-list").click(function() {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function() {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function() {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function() {
            adjustTabs();
        });
    });

    function adjustTabs() {
        var navTabs = $("#nav-tab");
        var moreDropdown = $("#more-dropdown");

        // Reset nav tabs
        navTabs.children().show();
        moreDropdown.empty();

        // Check if tabs overflow the container
        var visibleTabs = 7; // Number of visible tabs
        if ($(window).width() < 576) { // Adjust for mobile devices
            visibleTabs = 3; // Display only one tab on mobile
        } else if ($(window).width() > 576) {
            visibleTabs = 7;
        } else {
            visibleTabs = 7;
        }


        var hiddenTabs = navTabs.children(":gt(" + (visibleTabs) + ")");

        hiddenTabs.hide().appendTo(moreDropdown);

        // If there are hidden tabs, show the "More" dropdown
        if (hiddenTabs.length > 0) {
            moreDropdown.show();
        } else {
            moreDropdown.hide();
        }
    }
</script>

<script>
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><""B><f>r><"billList_wrapper"t><ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    // extend: 'collection',
                    // text: '<ion-icon name="download-outline"></ion-icon> Export',
                    // buttons: []
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;


        function full_datatable() {
            let fromDate = "<?= $fromDate ?>"; // For Date Filter
            let toDate = "<?= $toDate ?>"; // For Date Filter        
            let comid = <?= $company_id ?>;
            let locId = <?= $location_id ?>;
            let bId = <?= $branch_id ?>;

            $.ajax({
                type: "POST",
                url: "ajaxs/grn/ajax-manage-grn-invoice-all.php",
                dataType: 'json',
                data: {
                    act: 'alldata',
                },
                beforeSend: function() {

                },
                success: function(response) {
                    // all_data = response.all_data;
                    allData = response.all_data;


                },
            });
        };
        full_datatable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookiegrnInvoice');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/grn/ajax-manage-grn-invoice-all.php",
                dataType: 'json',
                data: {
                    act: 'grnInvoiceAll',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    // console.log(response);
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        $.each(responseObj, function(index, value) {
                            //  $('#item_id').val(value.itemId);

                            dataTable.row.add([
                                `<p>${value.sl_no}</p>`,
                                `<a href="#" class="soModal" data-ponumber="${value.po_number}" data-vendid="${value.vendor_id}" data-poid="${value.po_id}" data-toggle="modal" data-target="#viewGlobalModal">${value.po_number}</a>`,

                                `<p>${value.po_date}</p>`,
                                `<p>${value.ref_no}</p>`,
                                `<p>${value.trade_name}</p>`,
                                `<p>${value.vendor_code}</p>`,
                                `<p>${value.use_type}</p>`,
                                `<p>${value.totalAmount}</p>`,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        <li>
                                            <button class="soModal" data-toggle="modal" data-target="#viewGlobalModal" data-ponumber="${value.po_number}" data-vendid="${value.vendor_id}" data-poid="${value.po_id}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                    </ul>
                                </div>`


                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);

                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            // console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }


                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').hide();
                        $('#limitText').hide();
                    }
                }
            });
        }

        fill_datatable();



        $(document).on("click", ".soModal", function() {
            $('#viewGlobalModal').modal('show');
            let po_number = $(this).data('ponumber');
            let vendor_id = $(this).data('vendid');
            let po_id = $(this).data('poid');
            $('.ViewfirstTab').tab('show');
            $('.auditTrail').attr("data-ccode", po_number);

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/grn/ajax-manage-grn-invoice-modal.php",
                dataType: 'json',
                data: {
                    act: "modalData",
                    po_number,
                    vendor_id,
                    po_id
                },
                beforeSend: function() {
                    // $('.item-cards').remove();
                    $('#itemTableBody').html('');
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                        <div class="load-1">
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                        </div>
                    </div>`;

                    // Append the new HTML to the modal-body element
                    $('#viewGlobalModal .modal-body').append(loader);

                },
                success: function(value) {
                    // console.log(value);
                    if (value.status) {
                        let responseObj = value.data;
                        let itemObj = responseObj.itemdata
                        console.log(responseObj.trade_name)
                        $("#trade_name").html(responseObj.trade_name);
                        $("#vendor_name").html(responseObj.trade_name);
                        $("#vendor_code").html(responseObj.vendor_code);
                        $("#vendor_gstin").html(responseObj.vendor_gstin);
                        $("#totalAmount").html('₹ ' + responseObj.totalAmount);
                        $("#po_number").html(responseObj.po_number);
                        $("#ref_no").html('REF :&nbsp; ' + responseObj.ref_no);
                        $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                        $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                        $("#grnLink").attr('href', 'manage-manual-grn.php?view=' + po_number + '&type=grn');

                        $.each(itemObj, function(index, item) {


                            itemContent = `<div class="row body-state-table">
            
                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.itemCode}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${item.itemName}">${item.itemName}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.unitPrice}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.qty}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.qty} ${item.uom}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.total_price}</div>

                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.delivery_date}</div>
                                
                                
                            </div>`
                            $("#itemTableBody").append(itemContent);
                        })
                    }
                },
                complete: function() {
                    // $("#globalModalLoader").remove();
                    // $('#viewGlobalModal').modal('hide');
                    $('#viewGlobalModal .modal-body .load-wrapp').remove();
                }
            });
        });

        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiegrnInvoice')
                },
                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-paginationlist').prop('disabled', true)
                },

                success: function(response) {
                    // console.log(response);
                    var blob = new Blob([response.csvContentpage], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileName ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-paginationlistnew').prop('disabled', false);
                }
            })

        });
        $(document).on("click", ".ion-fulllistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/grn/ajax-manage-grn-invoice-all.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiegrnInvoice')
                },

                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-fulllistnew').prop('disabled', true)
                },
                success: function(response) {
                    var blob = new Blob([response.csvContentall], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileNameDownloadall ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-fulllistnew').prop('disabled', false)
                }
            })

        });



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#grnInvoiceLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {
            $(document).on("click", "#serach_submit", function(event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";
                    // let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    // let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'po_date') {
                        values = value2;
                    }

                    if ((columnSlag === 'po_date') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: values
                            }
                        };
                    } else {
                        formInputs[columnSlag] = {
                            operatorName,
                            value
                        };
                    }
                });

                $('#btnSearchCollpase_modal').modal('hide');
                // console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
                $("#myForm")[0].reset();
                $(".m-input2").remove();
            });


            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
            });

            $(document).on("click", "#serach_reset", function(e) {
                e.preventDefault();
                $("#myForm")[0].reset();
                $("#serach_submit").click();
            });

        });


        // -------------checkbox----------------------

        $(document).ready(function() {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                var columnVal = $(this).val();
                // console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        var removeIndex = indexValues.indexOf(index);
                        if (removeIndex !== -1) {
                            indexValues.splice(removeIndex, 1);
                        }
                    }
                    toggleColumnVisibility(index, this);
                });
            });

        });

    });

    //    -------------- save cookies--------------------

    $(document).ready(function() {
        $(document).on("click", "#check-box-submt", function(event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    fromData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            // console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'grnInvoice',
                        fromData: fromData
                    },
                    success: function(response) {
                        // console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });



    });
</script>

<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function() {
        $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'PO Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'PO Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>

<script>
    function openFullscreen() {
        var elem = document.getElementById("listTabPan")

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE11 */
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            $(".content-wrapper").removeClass("fullscreen-mode");
        } else {
            $(".content-wrapper").addClass("fullscreen-mode");
        }
    }
</script>