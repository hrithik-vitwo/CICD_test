<?php
$soTab = [
    [
        'name' => 'All',
        'href' => 'manage-sales-orders.php',
        'dataName'=>'all'
    ],
    [
        'name' => 'Pending SO',
        'href' => 'manage-sales-orders-pending.php',
        'dataName'=>'pending'
    ],
    [
        'name' => 'Open SO',
        'href' => 'manage-sales-orders-approved.php',
        'dataName'=>'open'
    ],
    [
        'name' => 'Exceptional SO',
        'href' => 'manage-sales-orders-exceptional.php',
        'dataName'=>'exceptional'
    ],
    [
        'name' => 'Item Order List',
        'href' => 'manage-sales-orders-item-wise.php',
        'dataName'=>'itemOrderList'
    ],
    [
        'name' => 'Pending Jobs',
        'href' => 'manage-job-order-pending-list.php',
        'dataName'=>'pendingJobs'
    ],
    [
        'name' => 'Done Jobs',
        'href' => 'manage-job-order-list.php',
        'dataName'=>'doneJobs'
    ]
];

// total row count for job order pending list
$pendingJOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus IN (14, 9, 11)")['data'];

// total row count for job order open list
$openJOCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus=10")['data'];

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


foreach ($soTab as $index => $data) {

    $totalRow;
    if ($data['href'] === 'manage-sales-orders.php') {
        $totalRow = $SOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-pending.php') {
        $totalRow = $pendingSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-approved.php') {
        $totalRow = $approvedSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-exceptional.php') {
        $totalRow = $exceptionalSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-item-wise.php') {
        $totalRow = $SOItemWiseCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-job-order-pending-list.php') {
        $totalRow = $pendingJOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-job-order-list.php') {
        $totalRow = $openJOCountObj['rowCount'];
    }
    $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";
    $noOfRecords = ($totalRow < 99) ? $totalRow : "99+"

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

        .activeNotification {
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

        .pulsing {
            animation: pulse 2s infinite;
            /* Apply the "pulse" animation for 2 seconds and repeat infinitely */
        }

        @media (max-width: 576px) {
            .activeNotification {
                position: absolute;
                top: 10px;
                right: 10px;
                animation: none;
                box-shadow: none;
                background: transparent !important;
                color: #003060;
                font-size: 0.7rem;
            }
        }
        .filter-link {
            cursor: pointer;
        }
    </style>


    <a class="filter-link <?= $activeClassName ?>" data-name=<?=$data['dataName']?>><ion-icon name="list-outline"></ion-icon><?= $data['name'] ?>
        <span class="activeNotification"><?= $noOfRecords ?></span>
    </a>
<?php } ?>

<script>
    $(document).ready(function() {
        $('.filter-link').on('click', function(e) {
            $('.filter-link').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>