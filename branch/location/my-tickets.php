<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/admin/func-bug-controller.php");
require_once("../../app/v1/functions/common/func-common.php");
require_once(BASE_DIR . "app/v1/fun-chat-controller.php");
$pageName = basename($_SERVER['PHP_SELF'], '.php');
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    //echo "Session Timeout";
    exit;
}
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");



if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST['bug_comment'])) {

    $comment = post_comment($_POST);
    swalToast($comment["status"], $comment["message"]);
}

if (isset($_POST['assign_task'])) {

    //console($_POST);

    $comment = assign_func($_POST);
    swalToast($comment["status"], $comment["message"]);
}


?>
<!-- .wrapper, body, html {
min-height: auto;
} -->

<style>
    .bgBlur {
        filter: blur(20px);
    }

    .chartContainer {
        width: 100%;
        height: 500px;
        margin-top: 6em;
    }

    .content-wrapper table tr:nth-child(2n+1) td {
        background: #b5c5d3;
    }

    tfoot.individual-search tr th {
        padding: 5px !important;
        border-right: 1px solid #fff !important;
    }

    .vertical-align {
        vertical-align: middle;
    }

    /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

    .dataTables_scrollHeadInner tr th {
        position: sticky;
        top: -1px;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row {
        display: flex !important;
        align-items: center;
        justify-content: end;
    }

    /* div.dataTables_wrapper {
    overflow: hidden;
  } */


    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(1),
    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        padding: 10px 20px;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 60% !important;
        appearance: none !important;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .dataTables_scroll {
        position: relative;
        margin-bottom: 10px;
    }

    .dataTables_scroll::-webkit-scrollbar {
        visibility: hidden;
    }

    .dataTables_scrollBody tfoot th {
        background: none !important;
    }

    .dataTables_scrollHead {
        margin-bottom: 4px;
    }

    .dataTables_scrollBody {
        max-height: 75vh !important;
        height: 75% !important;
        overflow: scroll !important;
    }

    .dataTables_scrollFoot {
        position: absolute;
        top: 37px;
        height: 50px;
        overflow-y: scroll;
    }

    .my-ticket-modal .modal-header {
        height: 140px !important;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 10px;
    }

    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
        border: 0;
    }

    .dataTables_filter {
        padding-right: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        padding: 0;
        border: 0;
    }

    .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0 20px;
        gap: 0;
    }

    /* .transactional-book-table tr td {
        white-space: pre-line !important;
    } */

    .dataTables_length {
        margin-left: 50px;
    }

    a.btn.add-col.setting-menu.waves-effect.waves-light {
        position: absolute !important;
        display: flex;
        justify-content: space-between;
        top: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 20px;
        position: relative;
        top: 0;
    }

    .dataTables_paginate {
        position: relative;
        right: 20px;
        bottom: 20px;
        margin-top: -15px;
    }

    .dt-center-in-div {
        display: block;
        /* order: 3; */
        margin-left: auto;
    }

    .dt-buttons.btn-group.flex-wrap button {
        background-color: #003060 !important;
        border-color: #003060 !important;
        border-radius: 7px !important;
    }

    /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

    .dt-buttons.btn-group.flex-wrap {
        gap: 10px;
    }


    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {

        display: block !important;

    }

    .dataTable thead tr th,
    .dataTable tfoot.individual-search tr th {
        padding-right: 30px !important;
        border-right: 0 !important;
    }

    select.fy-dropdown {
        max-width: 100px;
    }

    .report-wrapper .daybook-filter-list.filter-list {
        display: flex;
        gap: 6px;
        justify-content: flex-start;
        position: relative;
        top: 45px;
        left: 255px;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    .report-wrapper table tr td {
        background: #e7ebef;
    }

    .reports-card .filter-list a {
        background: #dedede;
        color: #003060;
        z-index: 9;
    }

    .report-wrapper .reports-card {
        background: #fff;
    }

    .report-wrapper table tr:nth-child(2n+1) td {
        background: #ffffff;
    }

    .label-select {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .img-bug-screenshot {
        width: 100%;
        height: 700px;
        object-fit: contain;
    }

    @media (max-width: 769px) {
        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: absolute;
            top: -39px;
            right: 60px;
        }

        .dt-buttons.btn-group.flex-wrap button {
            max-width: 60px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: -10px;
        }


    }

    @media (max-width :575px) {
        .dataTables_scrollFoot {
            position: absolute;
            top: 28px;
        }

        .dt-top-container {
            display: flex;
            align-items: baseline;
            padding: 0 20px;
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
        }



        div.dataTables_wrapper div.dataTables_length select {
            width: 164px !important;
        }

        .dt-center-in-div {
            margin: 3px auto;
        }

        div.dataTables_filter {
            right: 0;
            margin-top: 0;
            position: relative;
            right: -43px;
        }

        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: relative;
            top: 0;
            right: 0;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: 40px;
        }

        .dataTables_length label {
            font-size: 0;
        }
    }

    @media (max-width: 376px) {
        div.dataTables_wrapper div.dataTables_filter {
            margin-top: 0;
            padding-left: 0 !important;
        }



        div.dataTables_wrapper div.dataTables_filter input {
            max-width: 150px;
        }

        select.fy-dropdown {
            max-width: 100px;
        }



        /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
    }

    .report-wrapper .bug-report-table.dataTable thead tr th {
        text-align: left !important;
    }

    .pre-wrap {
        white-space: pre-wrap;
    }

    .page {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .marvel-device .screen {
        text-align: left;
    }

    .screen-container {
        height: 100%;
    }

    /* Status Bar */

    .status-bar {
        height: 25px;
        background: #004e45;
        color: #fff;
        font-size: 14px;
        padding: 0 8px;
        visibility: hidden;
    }

    .status-bar:after {
        content: "";
        display: table;
        clear: both;
    }

    .status-bar div {
        float: right;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        margin: 0 0 0 8px;
        font-weight: 600;
    }

    .message.sent img {
        max-width: 100%;
        width: auto;
        height: 230px;
        object-fit: contain;
    }

    /* Chat */

    .chat {
        height: calc(100% - 69px);
    }

    .chat-container {
        height: 100%;
    }

    /* User Bar */

    .user-bar {
        height: 55px;
        background: #003060;
        color: #fff;
        padding: 0 8px;
        font-size: 24px;
        position: relative;
        z-index: 1;
        border-radius: 12px 0;
        position: sticky;
        top: -30px;
        border-bottom: 1.5px solid #fff;
    }

    .user-bar:after {
        content: "";
        display: table;
        clear: both;
    }

    .user-bar div {
        float: left;
        transform: translateY(-50%);
        position: relative;
        top: 50%;
    }

    .user-bar .actions {
        float: right;
        margin: 5px 0 0 10px;
    }

    .user-bar .actions img {
        height: 28px;
    }

    .user-bar .actions.more {
        margin: 0 12px 0 20px;
    }

    .user-bar .actions.attachment {
        margin: 0 0 0 20px;
    }

    .user-bar .actions.attachment i {
        display: block;
        /*   transform: rotate(-45deg); */
    }

    .user-bar .avatar {
        margin: 0 0 0 5px;
        width: 36px;
        height: 36px;
    }

    .user-bar .avatar img {
        border-radius: 50%;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1);
        display: block;
        width: 100%;
    }

    .user-bar .name {
        font-size: 14px;
        font-weight: 600;
        text-overflow: ellipsis;
        letter-spacing: 0.3px;
        margin: 0 0 0 8px;
        overflow: hidden;
        white-space: nowrap;
        width: 170px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 5px 0;
    }

    .user-bar .status {
        display: block;
        font-size: 13px;
        font-weight: 400;
        letter-spacing: 0;
        padding: 5px 0 0;
        background: transparent;
        color: #fff;
    }

    .fileImageDisplay {
        display: none;
    }

    div.attachFileCard {
        position: absolute;
        width: 50%;
        height: 50%;
        top: 20%;
        right: 25%;
        background: #ccc;
        display: grid;
        place-content: center;
        display: none;
    }

    .fileImageDisplay {
        max-width: 50px;
        max-height: 50px;
        display: none;
    }

    /* Conversation */

    .conversation {
        height: 400px;
        position: relative;
        z-index: 0;
    }

    .conversation ::-webkit-scrollbar {
        transition: all 0.5s;
        width: 5px;
        height: 1px;
        z-index: 10;
    }

    .conversation ::-webkit-scrollbar-track {
        background: transparent;
    }

    .conversation ::-webkit-scrollbar-thumb {
        background: #b3ada7;
    }

    .conversation .conversation-container {
        width: 100%;
        /* height: calc(100% - 68px); */
        height: 441px;
        overflow: auto;
        padding: 0 16px;
        margin-bottom: 117px;
    }

    .conversation .conversation-container:after {
        content: "";
        display: table;
        clear: both;
    }

    /* Messages */

    .message {
        color: #000;
        clear: both;
        line-height: 20px;
        font-size: 12px;
        padding: 8px 20px;
        position: relative;
        margin: 8px 0;
        max-width: 80%;
        word-wrap: break-word;
        white-space: normal;
        z-index: -1;
    }

    .message:after {
        position: absolute;
        content: "";
        width: 0;
        height: 0;
        border-style: solid;
    }

    .metadata {
        display: flex;
        gap: 13px;
        float: right;
        padding: 0 0 0 7px;
        position: relative;
        bottom: -4px;
    }

    .metadata .time {
        color: #ffffff94;
        font-size: 9px;
        display: inline-block;
    }

    .message.sent .metadata .time {
        color: #003060;
    }

    .metadata .tick {
        display: inline-block;
        margin-left: 2px;
        position: relative;
        top: 4px;
        height: 16px;
        width: 16px;
    }

    .metadata .tick svg {
        position: absolute;
        transition: 0.5s ease-in-out;
    }

    .metadata .tick svg:first-child {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: perspective(800px) rotateY(180deg);
        transform: perspective(800px) rotateY(180deg);
    }

    .metadata .tick svg:last-child {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: perspective(800px) rotateY(0deg);
        transform: perspective(800px) rotateY(0deg);
    }

    .metadata .tick-animation svg:first-child {
        -webkit-transform: perspective(800px) rotateY(0);
        transform: perspective(800px) rotateY(0);
    }

    .metadata .tick-animation svg:last-child {
        -webkit-transform: perspective(800px) rotateY(-179.9deg);
        transform: perspective(800px) rotateY(-179.9deg);
    }

    .message:first-child {
        margin: 16px 0 8px;
    }

    .message.received {
        background: #003060;
        border-radius: 0px 2rem 2rem 2rem;
        float: left;
        color: #fff;
    }

    .message.received .metadata {
        padding: 0 0 0 16px;
    }

    .message.received:after {
        border-width: 0px 10px 10px 0;
        border-color: transparent #fff transparent transparent;
        top: 0;
        left: -10px;
    }

    .message.sent {
        background: #0030601f;
        border-radius: 2rem 0px 2rem 2rem;
        float: right;
        display: flex;
        align-items: center;
    }

    .message.sent:after {
        border-width: 0px 0 10px 10px;
        border-color: transparent transparent transparent #e0e6ec;
        top: 0;
        right: -10px;
    }

    .message.received.assignedMsg {
        font-size: 15px;
        ;
    }



    /* Compose */

    .conversation-compose {
        display: flex;
        flex-direction: row;
        gap: 7px;
        align-items: center;
        overflow: hidden;
        height: 44px;
        width: 100%;
        z-index: 2;
        border: 1px solid #ccc;
        border-radius: 30px;
        padding: 7px 0;
        background: #fff;
        position: sticky;
        bottom: -29px;
    }

    .conversation-compose div,
    .conversation-compose input {
        background: #fff;
        height: 100%;
    }

    .conversation-compose .attachment {
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        flex: 0 0 auto;
        margin-left: 8px;
        width: 38px;
        height: 38px;
        cursor: pointer;
    }

    .conversation-compose .attachment input {
        opacity: 0;
    }

    .conversation-compose .attachment i {
        position: absolute;
        font-size: 16px;
        color: #4f4f4f;
    }

    .conversation-compose .input-msg {
        border: 0;
        flex: 1 1 auto;
        font-size: 10px;
        margin: 0;
        outline: none;
        min-width: 50px;
        height: 36px;
    }

    .conversation-compose .photo {
        flex: 0 0 auto;
        border-radius: 0 30px 30px 0;
        text-align: center;
        width: auto;
        display: flex;
        padding-right: 6px;
        height: 38px;
    }

    .conversation-compose .photo img {
        display: block;
        color: #7d8488;
        font-size: 24px;
        transform: translate(-50%, -50%);
        position: relative;
        top: 50%;
        margin-left: 10px;
    }

    .conversation-compose .send {
        background: transparent;
        border: 0;
        cursor: pointer;
        flex: 0 0 auto;
        margin-right: 8px;
        padding: 0;
        position: relative;
        outline: none;
        margin-left: 0.5rem;
    }

    .conversation-compose .send .circle {
        background: #003060;
        border-radius: 50%;
        color: #fff;
        position: relative;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .conversation-compose .send .circle i {
        font-size: 13px;
    }

    /* Small Screens */

    @media (max-width: 768px) {
        .marvel-device.nexus5 {
            border-radius: 0;
            flex: none;
            padding: 0;
            max-width: none;
            overflow: hidden;
            height: 100%;
            width: 100%;
        }

        .marvel-device>.screen .chat {
            visibility: visible;
        }

        .marvel-device {
            visibility: hidden;
        }

        .marvel-device .status-bar {
            display: none;
        }

        .screen-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .conversation {
            height: calc(100vh - 55px);
        }

        .conversation .conversation-container {
            height: calc(100vh - 120px);
        }
    }

    .ticket-wrapper .fy-custom-section,
    .ticket-wrapper .custom-Range {
        flex-direction: row;
    }
</style>

<link rel="stylesheet" href="../../../public/assets/listing.css">
<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../../public/assets/core.js"></script>
<script src="../../../public/assets/charts.js"></script>
<script src="../../../public/assets/animated.js"></script>
<script src="../../../public/assets/forceDirected.js"></script>
<script src="../../../public/assets/sunburst.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery Plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- Chart Libraries -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<!-- OR include v3 libraries if you’re using v3 -->

<!-- Apex Charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>



<?php
// One single Query
// console($_SESSION);


if (isset($_GET['detailed-view'])) {


?>
    <!-- Content Wrapper detailed-view -->
    <div class="content-wrapper report-wrapper ticket-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Bug List</h3>
                                        </div>

                                        <div class="fy-custom-section">
                                            <div class="fy-dropdown-section">
                                                <?php
                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` ORDER BY `year_variant_id` DESC", true);

                                                if (isset($_POST['from_date'])) {
                                                    $f_date = $_POST['from_date'];
                                                    $to_date = $_POST['to_date'];
                                                    //echo 1;


                                                } else {

                                                    $start = explode('-', $variant_sql['data'][0]['year_start']);
                                                    $end = explode('-', $variant_sql['data'][0]['year_end']);
                                                    $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                    $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                                    $_POST['from_date'] = $f_date;
                                                    $_POST['to_date'] = $to_date;
                                                    $_POST['drop_val'] = 'fYDropdown';
                                                    $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                                }

                                                ?>
                                                <h6 class="text-xs font-bold">Days</h6>
                                                <div class="dropdown-fyear">

                                                    <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                                        <option value="">--Select One--</option>
                                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                                                echo "selected";
                                                                            } ?>>Today Report</option>
                                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                                                echo "selected";
                                                                            } ?>>Last 7 Days</option>
                                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                                                echo "selected";
                                                                            } ?>>Last 15 Days</option>
                                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                                                echo "selected";
                                                                            } ?>>Last 30 Days</option>
                                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                echo "selected";
                                                                            } ?>>Last 45 Days</option>
                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                echo "selected";
                                                                            } ?>>Last 60 Days</option>
                                                    </select>
                                                </div>
                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                            </div>

                                            <div class="customrange-section">
                                                <h6 class="text-xs font-bold">Custom Range</h6>
                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                    <div class="date-range-input d-flex">
                                                        <div class="form-input">
                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                        </div>
                                                        <div class="form-input">
                                                            <label class="mb-0" for="">TO</label>
                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                </form>
                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                            </div>

                                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                        </div>

                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed View</a>
                            </div>
                            <div class="card card-tabs mb-0" style="border-radius: 20px;">

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php
                                        $cond = '';

                                        $sql_list = "SELECT * FROM `erp_bug_list`  WHERE created_by='" . $created_by . "' AND `created_at` BETWEEN '" . $f_date . "' AND '" . $to_date . "'";

                                        $queryset = queryGet($sql_list, true);
                                        //console($queryset);
                                        $num_list = $queryset['numRows'];

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox = unserialize($settingsCh);
                                        //console($settingsCheckbox);
                                        $i = 0;
                                        ?>

                                        <table id="dataTable" class="table table-hover transactional-book-table bug-report-table" style="width: 100%; position: relative;">

                                            <thead>
                                                <?php
                                                $i++;
                                                ?>
                                                <tr>
                                                    <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>SL NO.</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Ticket ID</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Module Name</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Sub Module Name</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Page Name</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Page URL</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Bug Description</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Image URL</th>
                                                    <?php }
                                                    $i++;


                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Created By</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Created At</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Updated By</th>
                                                    <?php }
                                                    $i++;
                                                    if (in_array($i, $settingsCheckbox)) { ?>
                                                        <th>Updated At</th>
                                                    <?php } ?>

                                                    <th>Status</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php

                                                if ($num_list > 0) {
                                                    $i = 1;
                                                    $datas = $queryset['data'];
                                                    $sl = 0;
                                                    foreach ($datas as $data) {
                                                        // console($data);
                                                        $imagePath = $data['image_url'] ?: ($data['extra_images'] ?: null);
                                                        // console($data['extra_images']);
                                                        // echo "imagePath".$imagePath;
                                                        $finalUrl = $imagePath ? getFileUrlS3('upload/bugimages/' . $imagePath) : '--';
                                                        $i = 1;
                                                        //console($data);
                                                        $sql_Obj = queryGet("SELECT fldAdminName FROM `erp_bug_user_details` WHERE fldAdminKey=(SELECT assign_to FROM `erp_bug_list` WHERE `id`='" . $data['id'] . "');");
                                                        $adminName = $sql_Obj['data']['fldAdminName'];

                                                        $sl++;
                                                ?>
                                                        <tr>
                                                            <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $sl; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo ($data['bug_code']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['module_name'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['sub_module_name']; ?></td>
                                                            <?php }

                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $data['page_name']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $data['page_url']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="pre-wrap"> <?php echo $data['bug_description']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><img width="120" height="60" src="<?= $finalUrl?>" alt=""> </td>
                                                            <?php }
                                                            $i++;

                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo getCreatedByUser($data['created_by']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo formatDateORDateTime($data['created_at']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo getCreatedByUser($data['updated_by']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo formatDateORDateTime($data['updated_at']); ?></td>
                                                            <?php }
                                                            ?>

                                                            </td>
                                                            <td><?= $data['status'] ?></td>
                                                            <td>
                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo<?= $data['id'] ?>" data-image-url="<?= getFileUrlS3('upload/bugimages/' . $data['image_url']) ?>" data-extra-image="<?= getFileUrlS3('upload/bugimages/' . $data['extra_images']) ?>" class="btn btn-sm waves-effect waves-light" onclick="getChatHistory(<?= $data['id'] ?>)"><i class="fa fa-eye po-list-icon"></i></a>
                                                                <div class="modal fade right customer-modal my-ticket-modal" id="fluidModalRightSuccessDemo<?= $data['id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                        <!--Content-->
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                    <li class="nav-item waves-effect waves-light">
                                                                                        <a class="nav-link active" id="home" data-toggle="tab" href="#home<?= $data['id'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                                    </li>
                                                                                    <li class="nav-item waves-effect waves-light">
                                                                                        <a class="nav-link" id="conver" data-toggle="tab" href="#conver<?= $data['id'] ?>" role="tab" aria-controls="conver" aria-selected="false">Conversation</a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="tab-content" id="myTabContent">
                                                                                    <div class="tab-pane fade show active" id="home<?= $data['id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                        <img src="" class="img-bug-screenshot">

                                                                                    </div>
                                                                                    <!-- <div class="tab-pane fade show active" id="home<?= $data['id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                        <img src="" class="extra-image">

                                                                                    </div> -->

                                                                                    <div class="tab-pane fade unique_box" id="conver<?= $data['id'] ?>" role="tabpanel" aria-labelledby="conver-tab">

                                                                                        <div class="page">
                                                                                            <div class="marvel-device nexus5" style="margin-top: -3rem;">
                                                                                                <div class="top-bar"></div>
                                                                                                <div class="sleep"></div>
                                                                                                <div class="volume"></div>
                                                                                                <div class="camera"></div>
                                                                                                <div class="screen">
                                                                                                    <div class="screen-container">
                                                                                                        <div class="status-bar">
                                                                                                            <div class="time"></div>
                                                                                                            <div class="battery">
                                                                                                                <i class="zmdi zmdi-battery"></i>
                                                                                                            </div>
                                                                                                            <div class="network">
                                                                                                                <i class="zmdi zmdi-network"></i>
                                                                                                            </div>
                                                                                                            <div class="wifi">
                                                                                                                <i class="zmdi zmdi-wifi-alt-2"></i>
                                                                                                            </div>
                                                                                                            <div class="star">
                                                                                                                <i class="zmdi zmdi-star"></i>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="chat">
                                                                                                            <div class="chat-container" id="chat-con<?= $data['id'] ?>">
                                                                                                                <div class="user-bar">
                                                                                                                    <div class="back">
                                                                                                                        <i class="zmdi zmdi-arrow-left"></i>
                                                                                                                    </div>
                                                                                                                    <div class="avatar">
                                                                                                                        <img src="https://cdn-icons-png.flaticon.com/128/3135/3135715.png" alt="Avatar">
                                                                                                                    </div>

                                                                                                                    <div class="name">
                                                                                                                        <span><?= isset($adminName) ? $adminName : 'Administrator' ?></span>
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                                <div class="conversation">
                                                                                                                    <div class="conversation-container" id="conv_id<?= $data["id"] ?>">
                                                                                                                        <?php if (isset($adminName)) { ?>
                                                                                                                            <div class="message received assignedMsg">
                                                                                                                                <p> Hi, <?= $adminName ?> has been assigned to this task. Feel free to connect here for any assistance or if you encounter any challenges in the future.</p>
                                                                                                                            </div>
                                                                                                                        <?php }
                                                                                                                        ?>

                                                                                                                    </div>
                                                                                                                    <form class="conversation-compose" method="POST" id="convFormId<?= $data["id"] ?>" enctype="multipart/form-data">
                                                                                                                        <div class="attachment">
                                                                                                                            <i class="fas fa-paperclip po-list-icon"></i>
                                                                                                                            <input type="file" style="width: 10px;" name="" id="attachFile<?= $data['id'] ?>" onchange="displayFileName(<?= $data['id'] ?>)">
                                                                                                                        </div>
                                                                                                                        <input class="input-msg" id="input-msg<?= $data['id'] ?>" name="input" placeholder="Type a mes.." autocomplete="off" autofocus>

                                                                                                                        <button type="buttton" class="send" id="send_btn" onclick="sendMessage(event,<?= $data['id'] ?>)">
                                                                                                                            <div class="circle">
                                                                                                                                <i class="fas fa-paper-plane"></i>
                                                                                                                            </div>
                                                                                                                        </button>
                                                                                                                    </form>

                                                                                                                    <div id="attachFileCard<?= $data['id'] ?>" class="attachFileCard">
                                                                                                                        <span id="fileNameDisplay"></span>
                                                                                                                        <img id="fileImageDisplay<?= $data['id'] ?>" class="fileImageDisplay" src="" alt="File Image">
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
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
                                                <?php } else {
                                                } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('a[data-toggle="modal"][data-target^="#fluidModalRightSuccessDemo"]').on('click', function() {
                                            if($(this).data('image-url') !='File not found on S3'){
                                                 $('.img-bug-screenshot').attr('src', $(this).data('image-url'));
                                            }else{
                                                 $('.img-bug-screenshot').attr('src', $(this).data('extra-image'));
                                            }



                                        });
                                    });
                                </script>

                                <!-- chat function -->
                                <!---------------------------------Detailed View  Table settings Model Start--------------------------------->

                                <div class="modal" id="myModal2">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Detailed View Column Settings</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                                <div class="modal-body" style="max-height: 450px;">
                                                    <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                    <input type="hidden" name="pageTableName" value="ERP_REPORT_DETAILED_VIEW_<?= $pageName ?>" />
                                                    <div class="modal-body">
                                                        <div id="dropdownframe"></div>
                                                        <div id="main2">
                                                            <?php $p = 1; ?>
                                                            <table>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        SL NO.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Ticket ID</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Module Name</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Sub Module Name</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Page Name</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Page URL</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Bug Description</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Image URL</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Created By</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Created At</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Updated By</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Updated At</td>
                                                                </tr>
                                                            </table>
                                                        </div>
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
        </section>
        <!-- /.content -->
    </div>
    <!-- /.Content Wrapper detailed-view-->

<?php
} else {

?>
    <!-- Content Wrapper. Graph View -->
    <div class="content-wrapper report-wrapper ticket-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Bug List</h3>
                                        </div>

                                        <div class="fy-custom-section">
                                            <div class="fy-dropdown-section">
                                                <?php
                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` ORDER BY `year_variant_id` DESC", true);

                                                if (isset($_POST['from_date'])) {
                                                    $f_date = $_POST['from_date'];
                                                    $to_date = $_POST['to_date'];
                                                    //echo 1;


                                                } else {

                                                    $start = explode('-', $variant_sql['data'][0]['year_start']);
                                                    $end = explode('-', $variant_sql['data'][0]['year_end']);
                                                    $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                    $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                                    $_POST['from_date'] = $f_date;
                                                    $_POST['to_date'] = $to_date;
                                                    $_POST['drop_val'] = 'fYDropdown';
                                                    $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                                }

                                                ?>
                                                <h6 class="text-xs font-bold">Days</h6>
                                                <div class="dropdown-fyear">

                                                    <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                                        <option value="">--Select One--</option>
                                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                                                echo "selected";
                                                                            } ?>>Today Report</option>
                                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                                                echo "selected";
                                                                            } ?>>Last 7 Days</option>
                                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                                                echo "selected";
                                                                            } ?>>Last 15 Days</option>
                                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                                                echo "selected";
                                                                            } ?>>Last 30 Days</option>
                                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                echo "selected";
                                                                            } ?>>Last 45 Days</option>
                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                echo "selected";
                                                                            } ?>>Last 60 Days</option>
                                                    </select>
                                                </div>
                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                            </div>

                                            <div class="customrange-section">
                                                <h6 class="text-xs font-bold">Custom Range</h6>
                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                    <div class="date-range-input d-flex">
                                                        <div class="form-input">
                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                        </div>
                                                        <div class="form-input">
                                                            <label class="mb-0" for="">TO</label>
                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                </form>
                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                            </div>

                                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                        </div>

                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                            </div>

                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">

                                    <?php
                                    //Graph View SQL

                                    $open_sql = queryGet("SELECT COUNT(`id`) as  total_open FROM `erp_bug_list` WHERE  created_by='" . $created_by . "' AND `status`!='solved'");
                                    $solved_sql = queryGet("SELECT COUNT(`id`) as total_solved FROM `erp_bug_list` WHERE  created_by='" . $created_by . "' AND `status`='solved'");

                                    $open = $open_sql['data']['total_open'];
                                    $solved = $solved_sql['data']['total_solved'];

                                    $queryset = [];
                                    $queryset[] = array(
                                        'open' => $open,
                                        'solved' => $solved
                                    );
                                    //console($queryset);


                                    $chartData = json_encode($queryset, true);

                                    ?>

                                    <div class="container-fluid mt-10">

                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 d-flex">
                                                <div class="card flex-fill reports-card">
                                                    <div class="card-body">
                                                        <div id="chartdiv" class="chartContainer"></div>
                                                    </div>
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
    <!-- /.Content Wrapper. Graph View -->


<?php
}
require_once("../common/footer.php");
?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>

<script>
    /* Meme */

    var memes = [
        'I am Good...'
    ];

    var random = document.querySelector("#random");

    random.innerHTML = memes[Math.floor(Math.random() * memes.length)];

    /* Time */

    var deviceTime = document.querySelector(".status-bar .time");
    var messageTime = document.querySelectorAll(".message .time");

    deviceTime.innerHTML = moment().format("h:mm");

    setInterval(function() {
        deviceTime.innerHTML = moment().format("h:mm");
    }, 1000);

    for (var i = 0; i < messageTime.length; i++) {
        messageTime[i].innerHTML = moment().format("h:mm A");
    }

    /* Message */

    // var form = document.querySelector(".conversation-compose");
    // var conversation = document.querySelector(".conversation-container");


    //form.addEventListener("submit", newMessage());

    // function newMessage(e,$id) {
    //    // var input = e.target.input;
    //     var input = document.getElementById('input-msg'+$bug_Id);


    //     if (input.value) {
    //         var message = buildMessage(input.value);
    //         conversation.appendChild(message);
    //         animateMessage(message);
    //     }

    //     input.value = "";
    //     conversation.scrollTop = conversation.scrollHeight;

    //     e.preventDefault();
    // }
</script>


<script>
    function table_settings_concised_view() {
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

    $(document).ready(function() {

        $("#dataTable tfoot th").each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var columnSl = 0;
        var table = $("#dataTable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            buttons: ['copy', 'csv', 'excel', 'print'],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,


        });


    });
</script>

<script>
    var elem = document.getElementById("listTabPan");

    function openFullscreen() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            /* IE11 */
            elem.msRequestFullscreen();
        }
    }
</script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            },
            function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
    });
</script>

<!-- CHANGES -->
<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    $('#fYDropdown').change(function() {
        var title = $(this).val();
        if (title == "customrange") {
            $("#drop_val").val('customrange');
            $("#from_date").val('');
            $("#to_date").val('');
            $("#from_date").focus();
        } else {
            let start = $(this).find(':selected').data('start');
            let end = $(this).find(':selected').data('end');
            //alert(start);
            $("#from_date").val(start);
            $("#to_date").val(end);
            $("#drop_val").val('fYDropdown');
            $("#drop_id").val(title);
            $('#date_form').submit();
        }
    });

    $('#quickDropdown').change(function() {
        var days = $(this).val();
        var today = new Date();
        var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

        var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

        // alert(start);
        // alert(end);
        $("#from_date").val(start);
        $("#to_date").val(end);
        $("#drop_val").val('quickDrop');
        $("#drop_id").val(days);

        $('#date_form').submit();
    });

    function compare_date() {
        let fromDate = $("#from_date").val();
        let toDate = $("#to_date").val();

        const date1 = new Date(fromDate);
        const date2 = new Date(toDate);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));


        if (fromDate && toDate) {
            if (diffDays > 366) {
                document.getElementById("rangeid").disabled = true;
                $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
            } else {
                $(".customRangeCla").html('');
                document.getElementById("rangeid").disabled = false;

                if (toDate < fromDate) {
                    $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
                    document.getElementById("rangeid").disabled = true;

                } else {
                    $(".customRangeCla").html('');
                    document.getElementById("rangeid").disabled = false;
                }
            }
        }
    }
</script>
<!-- CHANGES -->


<!-- CHART FUNCTION -->
<script>
    var chartData = <?php echo $chartData; ?>;

    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart
        var chart = am4core.create("chartdiv", am4charts.PieChart);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.data = [{
            "category": "Open",
            "value": Number(chartData[0].open),
        }, {

            "category": "Solved",
            "value": Number(chartData[0].solved),
        }];
        var series = chart.series.push(new am4charts.PieSeries());
        series.dataFields.value = "value";
        series.dataFields.radiusValue = "value";
        series.dataFields.category = "country";
        series.slices.template.cornerRadius = 2;
        series.colors.step = 3;

        series.hiddenState.properties.endAngle = -120;

        //chart.legend = new am4charts.Legend();

    });
</script>


<script>
    function displayFileName(id) {
        var input = document.getElementById('attachFile' + id);
        var attachCard = document.getElementById('attachFileCard' + id);
        var fileNameDisplay = document.getElementById('fileNameDisplay');
        var fileImageDisplay = document.getElementById('fileImageDisplay' + id);
        var conversation = document.querySelector('.conversation-container');

        if (input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;

            var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'xlsx'];

            var fileType = input.files[0].type.split('/')[0]; // Get the file type (e.g., 'image')

            if (allowedTypes.includes(input.files[0].type)) {

                if (fileType === 'image') {
                    attachCard.style.display = 'grid';
                    fileImageDisplay.src = URL.createObjectURL(input.files[0]);
                    fileImageDisplay.style.display = 'inline-block';
                    // conversation.style.filter = 'blur(8px)';
                } else if (fileType === 'pdf') {
                    attachCard.style.display = 'grid';
                    fileImageDisplay.src = URL.createObjectURL(input.files[0]);
                    fileImageDisplay.style.display = 'none';
                    // conversation.style.filter = 'blur(8px)';
                } else if (fileType === 'xlsx') {
                    attachCard.style.display = 'grid';
                    fileImageDisplay.src = URL.createObjectURL(input.files[0]);
                    fileImageDisplay.style.display = 'none';
                    // conversation.style.filter = 'blur(8px)';
                } else {
                    fileImageDisplay.style.display = 'none';
                }

            } else {
                alert('Invalid file type. Please select a PDF, JPG, PNG, or XLSX file.');
                input.value = '';
            }
        }
    }


    function sendMessage(e, $bug_Id) {
        e.preventDefault();
        var input = document.getElementById('input-msg' + $bug_Id);
        var bug_id = $bug_Id;
        var created_by = '<?= $created_by ?>';
        var updated_by = '<?= $updated_by ?>';

        var conversation = document.getElementById('conv_id' + bug_id);

        var inputValue = input.value;
        if (inputValue || document.getElementById('attachFile' + bug_id).files.length > 0) {
            var message = buildMessage(inputValue, bug_id);
            conversation.appendChild(message);
            animateMessage(message);

        }
        var form = document.getElementById('convFormId' + bug_id);
        var formData = new FormData(form);
        formData.append('attachedFile', document.getElementById('attachFile' + bug_id).files[0]);
        formData.append('bug_id', bug_id);
        formData.append('created_by', created_by);
        formData.append('updated_by', updated_by);
        formData.append('action', 'insert');

        $.ajax({
            type: 'POST',
            url: '<?php echo BASE_URL ?>api/v2/bugs/chat-api.php',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                console.log("Sending message");
            },
            success: function(response) {
                console.log("Response", response);
                // Handle the response as needed
            },
            error: function(xhr, textStatus, error) {
                console.log("Error", textStatus, error);
            }
        });


        input.value = '';
        document.getElementById('attachFile' + bug_id).value = '';
        document.getElementById('attachFileCard' + bug_id).style.display = 'none'; // Hide the attachment card
        document.getElementById('fileNameDisplay').textContent = '';
        document.getElementById('fileImageDisplay' + bug_id).style.display = 'none';
        conversation.style.filter = 'blur(0px)';

        conversation.scrollTop = conversation.scrollHeight;

        // conversation.style.filter = 'blur(0px)';

    }

    function buildMessage(text, id) {

        var element = document.createElement('div');

        element.classList.add('message', 'sent');

        if (text || document.getElementById('attachFile' + id).files.length > 0) {
            element.innerHTML = text ? text + '<span class="metadata">' +
                '<span class="time">' +
                moment().format('h:mm A') +
                '</span>' +
                '<span class="tick tick-animation">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck" x="2047" y="2061"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#92a58c"/></svg>' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060"/></svg>' +
                '</span>' +
                '</span>' : `<img src="${document.getElementById('fileImageDisplay'+id).src}" alt="Image File">` +
                '<span class="metadata">' +
                '<span class="time">' +
                moment().format('h:mm A') +
                '</span>' +
                '<span class="tick tick-animation">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck" x="2047" y="2061"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#92a58c"/></svg>' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060"/></svg>' +
                '</span>' +
                '</span>';


        }
        return element;
    }

    function animateMessage(message) {
        setTimeout(function() {
            var tick = message.querySelector('.tick');
            tick.classList.remove('tick-animation');
        }, 500);


    }

    function getChatHistory(bug_id) {
        $.ajax({
            type: 'GET',
            url: '<?php echo BASE_URL ?>api/v2/bugs/chat-api.php',
            data: {
                action: 'chats',
                bug_id: bug_id
            },
            success: function(response) {
                let chatHtml = ``;
                console.log(response);
                response.data.forEach(function(messageData) {
                    const {
                        conversation,
                        attatch,
                        time,
                        image_check,
                        created_by
                    } = messageData;

                    var fileObj;
                    var attachmentObj = "text.jpg";

                    if (attachmentObj.endsWith("jpg") || attachmentObj.endsWith("png")) {
                        fileObj = `<img src="${image_check}" alt="Image File" class="attach_img" height="200" width="500">`;
                    } else if (attachmentObj.endsWith("pdf")) {
                        fileObj = `<iframe src="${image_check}" class="pdf"></iframe>`;
                    } else if (attachmentObj.endsWith("xlsx")) {
                        fileObj = `<iframe src="" class="xlsx"></iframe>`;
                    }


                    if (created_by.endsWith("admin") && !image_check) {
                        chatHtml += `
                    <div class="message sent">${conversation}
                        <span class="metadata">
                            <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                            </svg></span>
                         </span>
                    </div>
                    `;
                        console.log(response);
                    } else if (created_by.endsWith("admin") && image_check) {
                        // Add the image dynamically to the chatHtml
                        chatHtml += `
                                        <div class="message sent">
                                        ${fileObj}
                                            <span class="metadata">
                                                <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                                                </svg></span>
                                            </span>
                                        </div>
                                        `;



                    } else if (created_by.endsWith("Performer") && !image_check) {
                        //alert(0);
                        chatHtml += `
                   
                    <div class="message received">${conversation}
                        <span class="metadata">
                            <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                            </svg></span>
                         </span>
                    </div>
                    `;
                        console.log(response);
                    } else if (created_by.endsWith("Performer") && image_check) {
                        // alert(1);
                        // Add the image dynamically to the chatHtml
                        chatHtml += `
                                        <div class="message received">
                                        ${fileObj}
                                            <span class="metadata">
                                                <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                                                </svg></span>
                                            </span>
                                        </div>
                                        `;



                    } else if (created_by.endsWith("location") && !image_check) {
                        //  alert(0);
                        chatHtml += `
                   
                    <div class="message sent">${conversation}
                        <span class="metadata">
                            <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                            </svg></span>
                         </span>
                    </div>
                    `;
                        console.log(response);
                    } else if (created_by.endsWith("location") && image_check) {
                        // alert(1);
                        // Add the image dynamically to the chatHtml
                        chatHtml += `
                                        <div class="message sent">
                                        ${fileObj}
                                            <span class="metadata">
                                                <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                                                </svg></span>
                                            </span>
                                        </div>
                                        `;



                    } else {
                        chatHtml += `
                                <div class="message received attachment">
                                     ${fileObj}
                                            <span class="metadata">
                                                <span class="time">${time}</span><span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                                    <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#003060" />
                                                </svg></span>
                                            </span>
                                     </div>
                                     `;

                    }
                });
                $(`#conv_id${bug_id}`).html(chatHtml);
            },
            error: function(response) {

                console.error('AJAX request failed---------', response);
            }
        });


    }
</script>