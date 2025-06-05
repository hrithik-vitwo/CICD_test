<?php
$QLTab = [
    [
        'name' => 'All',
        'dataName'=>'all'
    ],
    [
        'name' => 'Pending',
        'dataName'=>'pending'
    ],
    [
        'name' => 'Approved',
        'dataName'=>'approved'
    ],
    [
        'name' => 'Accepted',
        'dataName'=>'accepted'
    ],
    [
        'name' => 'Rejected',
        'dataName'=>'rejected'
    ],
    [
        'name' => 'Closed',
        'dataName'=>'closed'
    ]
];

// total row count for job order pending list
$allQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                           LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                           WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted' 
                           AND so.location_id='" . $location_id . "'")['data'];

$pendingQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                              LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                              WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted'
                              AND so.location_id='" . $location_id . "' AND stat.label='pending'")['data'];

$approvedQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                               LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                               WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted'
                               AND so.location_id='" . $location_id . "' AND stat.label='approved'")['data'];

$acceptedQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                               LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                               WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted'
                               AND so.location_id='" . $location_id . "' AND stat.label='accepted'")['data'];

$rejectedQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                               LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                               WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted'
                               AND so.location_id='" . $location_id . "' AND stat.label='rejected'")['data'];

$closedQLCountObj = queryGet("SELECT count(*) AS rowCount FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so 
                             LEFT JOIN `erp_status_master` AS stat ON so.approvalStatus=stat.status_id 
                             WHERE so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND `status` !='deleted'
                             AND so.location_id='" . $location_id . "' AND stat.label='closed'")['data'];


foreach ($QLTab as $index => $data) {

    $totalRow = 0; // Initialize totalRow
    switch ($data['dataName']) {
        case 'all':
            $totalRow = $allQLCountObj['rowCount'];
            break;
        case 'pending':
            $totalRow = $pendingQLCountObj['rowCount'];
            break;
        case 'approved':
            $totalRow = $approvedQLCountObj['rowCount'];
            break;
        case 'accepted':
            $totalRow = $acceptedQLCountObj['rowCount'];
            break;
        case 'rejected':
            $totalRow = $rejectedQLCountObj['rowCount'];
            break;
        case 'closed':
            $totalRow = $closedQLCountObj['rowCount'];
            break;
        default:
            $totalRow = 0; // Fallback for unmatched dataName
            break;
    }
    $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";
    $activeClassName = $data['dataName'] == "all" ? "active" : "";
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


    <a class="filter-link filterList <?= $activeClassName ?>" data-name=<?=$data['dataName']?>><ion-icon name="list-outline"></ion-icon><?= $data['name'] ?>
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