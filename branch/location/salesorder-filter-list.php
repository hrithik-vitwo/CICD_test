<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("boq/controller/boq.controller.php");

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$boqControllerObj = new BoqController();

global $company_id;
global $branch_id;
global $location_id;

// total row count for job order pending list
$pendingJOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus IN (14, 9, 11)")['data'];

// total row count for job order open list
$openJOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus=14")['data'];

// total row count for sales order exceptional list
$exceptionalSOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND approvalStatus=12")['data'];

// total row count for sales order pending list
$pendingSOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND approvalStatus=14")['data'];

// total row count for sales order approved list
$approvedSOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND approvalStatus=9")['data'];

// total row count for sales order list
$SOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "'")['data'];

// total row count for sales order item wise list
$SOItemWiseCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` as items, `" . ERP_BRANCH_SALES_ORDER . "` as sales_order, `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` as delivery WHERE sales_order.company_id='" . $company_id . "'  AND sales_order.branch_id='" . $branch_id . "'  AND sales_order.location_id='" . $location_id . "' AND sales_order.so_id = items.so_id AND sales_order.approvalStatus != 14 AND items.so_item_id = delivery.so_item_id")['data'];

// console($SOItemWiseCountObj);

$urlName = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);

$soActive = "";
$soPActive = "";
$soEActive = "";
$soIWActive = "";
$joPActive = "";
$joDActive = "";

if ($urlName == "manage-sales-orders.php") {
    $soActive = "active element-to-pulse";
} else if ($urlName == "manage-sales-orders-pending.php") {
    $soPActive = "active element-to-pulse";
} else if ($urlName == "manage-sales-orders-approved.php") {
    $soApprovedActive = "active element-to-pulse";
} else if ($urlName == "manage-sales-orders-exceptional.php") {
    $soEActive = "active element-to-pulse";
} else if ($urlName == "manage-sales-orders-item-wise.php") {
    $soIWActive = "active element-to-pulse";
} else if ($urlName == "manage-job-order-pending-list.php") {
    $joPActive = "active element-to-pulse";
} else if ($urlName == "manage-job-order-list.php") {
    $joDActive = "active element-to-pulse";
}
?>

<style>
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.06);
        }

        100% {
            transform: scale(1);
        }
    }

    .inactiveNotification {
        position: absolute;
        font-weight: bolder;
        margin: 0;
        top: -11px;
        background: #003060 !important;
        color: #fff;
        font-size: 0.8em;
        padding: 1px 10px;
        border-radius: 50px 50px;
        right: -8px;
        box-shadow: inset -1px -1px 3px 0px #a4a9f0;
        animation: pulse 2s infinite;
        /* Add the animation here */
    }

    .activeNotification {
        position: absolute;
        font-weight: bolder;
        margin: 0;
        top: -11px;
        background: #4CAF50;
        color: #FAFAFA;
        font-size: 0.8em;
        padding: 1px 10px;
        border-radius: 50px 50px;
        right: -8px;
        /* border-top: 2px solid #003060;
    border-right: 2px solid #003060; */
        box-shadow: inset -1px -1px 3px 0px #a4a9f0;
        animation: pulse 2s infinite;
        /* Add the animation here */
    }

    .pulsing {
        animation: pulse 2s infinite;
        /* Apply the "pulse" animation for 2 seconds and repeat infinitely */
    }

    @media (min-width : 768px) {
        .filter-list {
            display: flex;
            top: 0;
            left: 0;
        }

        .filter-dropdown {
            display: none;
        }
    }


    @media (max-width : 768px) {

        .inactiveNotification {
            position: absolute;
            top: 5px;
            right: 14px;
        }

        .filter-dropdown {
            display: block;
            z-index: 9;
            box-shadow: none;
            position: relative;
            top: 0;
            left: 0;
            background: transparent;
            backdrop-filter: blur(16px);
        }

        .filter-dropdown.active {
            width: 150px;
            background: #fff9;
            position: absolute;
            right: 0;
            left: -75%;
        }

        .filter-list {
            display: none;
        }

        .dropdown-content {
            max-height: 0;
            width: 0;
            display: flex;
            overflow: hidden;
            flex-direction: column;
            gap: 0.5rem;
            transition: max-height 0.2s, width 0.4s;
        }

        .dropdown-content.active {
            width: 150px;
            max-height: 500px;
        }

        .dropdown-content a {
            font-size: 15px;
            max-width: 150px;
            position: relative;
            text-align: left;
            border-bottom: 1px solid #00000026 !important;
            border-radius: 0;
        }

        .dropdown-content a:last-child {
            border: 0;
        }

        .filter-dropdown button {
            border: 0;
            box-shadow: none;
            border-radius: 50%;
        }
    }

    @media (max-width : 375px) {
        .filter-dropdown.active {
            width: 150px;
            background: #fff9;
            position: absolute;
            right: 0;
            left: -45%;
        }
     }


    /* .filter-list a {
        display: none;
    }

    .filter-list .dropdown {
        display: grid;
    }

    .dropdown button {
        z-index: 999;
    } */
</style>
<div class="filter-list">
    <a href="manage-sales-orders.php" class="btn <?= $soActive ?>"><i class="fa fa-stream mr-2 active"></i>
        All
        <span class="<?= $soActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $SOCountObj['rowCount'] ?></span>
    </a>

    <!-- <a href="manage-sales-orders-approved.php" class="btn"><i class="fa fa-lock-open mr-2"></i>Open SO</a> -->
    <a href="manage-sales-orders-pending.php" class="btn <?= $soPActive ?>"><i class="fa fa-clock mr-2"></i>
        Pending SO
        <span class="<?= $soPActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $pendingSOCountObj['rowCount'] ?></span>
    </a>

    <a href="manage-sales-orders-approved.php" class="btn <?= $soApprovedActive ?>"><i class="fa fa-clock mr-2"></i>
        Open SO
        <span class="<?= $soApprovedActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $approvedSOCountObj['rowCount'] ?></span>
    </a>

    <a href="manage-sales-orders-exceptional.php" class="btn <?= $soEActive ?>"><i class="fa fa-exclamation-circle mr-2"></i>
        Exceptional SO
        <span class="<?= $soEActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $exceptionalSOCountObj['rowCount'] ?></span>
    </a>

    <a href="manage-sales-orders-item-wise.php" class="btn <?= $soIWActive ?>"><i class="fa fa-list mr-2"></i>
        Item Order List
        <span class="<?= $soIWActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $SOItemWiseCountObj['rowCount'] ?></span>
    </a>

    <a href="manage-job-order-pending-list.php" class="btn <?= $joPActive ?>"><i class="fa fa-list mr-2"></i>
        Pending Jobs
        <span class="<?= $joPActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $pendingJOCountObj['rowCount'] ?></span>
    </a>

    <a href="manage-job-order-list.php" class="btn <?= $joDActive ?>"><i class="fa fa-list mr-2"></i>
        Done Jobs
        <span class="<?= $joDActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $openJOCountObj['rowCount'] ?></span>
    </a>

</div>

<div class="dropdown filter-dropdown" id="filterDropdown">

    <button type="button" class="dropbtn" id="dropBtn">
        <i class="fas fa-filter po-list-icon"></i>
    </button>

    <div class="dropdown-content">
        <a href="manage-sales-orders.php" class="btn <?= $soActive ?>">All
            <span class="<?= $soActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $SOCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-sales-orders-pending.php" class="btn <?= $soPActive ?>">Pending SO
            <span class="<?= $soPActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $pendingSOCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-sales-orders-approved.php" class="btn <?= $soApprovedActive ?>">Open SO
            <span class="<?= $soApprovedActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $approvedSOCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-sales-orders-exceptional.php" class="btn <?= $soEActive ?>">Exceptional SO
            <span class="<?= $soEActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $exceptionalSOCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-sales-orders-item-wise.php" class="btn <?= $soIWActive ?>">Item Order List
            <span class="<?= $soIWActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $SOItemWiseCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-job-order-pending-list.php" class="btn <?= $joPActive ?>">Pending Jobs
            <span class="<?= $joPActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $pendingJOCountObj['rowCount'] ?></span>
        </a>
        <a href="manage-job-order-list.php" class="btn <?= $joDActive ?>">Done Jobs
            <span class="<?= $joDActive === 'active' ? 'activeNotification' : 'inactiveNotification' ?>"><?= $openJOCountObj['rowCount'] ?></span>
        </a>
    </div>
</div>

<script>
    // JavaScript code to add the "pulsing" class to inactive spans after page load
    document.addEventListener("DOMContentLoaded", () => {
        let inactiveNotifications = document.querySelectorAll('.element-to-pulse');

        inactiveNotifications.forEach(a => {
            a.classList.add("pulsing");
        });
    });

    // document.addEventListener("DOMContentLoaded", () => {

    // });


    $(document).ready(function() {
        $("#dropBtn").on("click", function(e) {
            e.stopPropagation(); // Stop the event from propagating to the document
            console.log("clickedddd");
            $("#filterDropdown .dropdown-content").addClass("active");
            $("#filterDropdown").addClass("active");
        });

        $(document).on("click", function() {
            $("#filterDropdown .dropdown-content").removeClass("active");
            $("#filterDropdown").removeClass("active");
        });

        // Close the dropdown when clicking inside it
        $("#filterDropdown .dropdown-content").on("click", function(e) {
            e.stopPropagation(); // Prevent the event from reaching the document
        });

        // $(window).resize(function() {
        //     if ($(window).width() > 768) {
        //         $("#filterDropdown .dropdown-content").hide();
        //     }
        // });
    });


    // $(document).ready(function() {
    //     $("#dropBtn").on("click", function() {
    //         console.log("clickedddd")
    //         // $("#filterDropdown .dropdown-content", "#filterDropdown .dropdown-content > a").show();
    //         $("#filterDropdown .dropdown-content").addClass("active");
    //         $("#filterDropdown").addClass("active");
    //     });
    //     $(document).on("click", function() {
    //         $("#filterDropdown .dropdown-content").removeClass("active");
    //         $("#filterDropdown").removeClass("active");
    //     })

    //     // $(window).resize(function() {
    //     //     if ($(window).width() > 768) {
    //     //         $("#filterDropdown .dropdown-content").hide();
    //     //     }
    //     // });
    // });
</script>