<?php
$soTab = [
    [
        'name' => 'All',
        'href' => 'goods-p.php'
    ],
    [
        'name' => 'Raw Materials',
        'href' => 'goods-type-items-p.php'
    ],
    [
        'name' => 'SFG',
        'href' => '/components/mm/goods-type-items-sfg.php'
    ],
    [
        'name' => 'FG',
        'href' => 'goods-type-items.php?fg'
    ],
    [
        'name' => 'Service',
        'href' => 'goods-type-items.php?service'
    ],
    [
        'name' => 'Assets',
        'href' => 'manage-assets.php'
    ]
];



foreach ($soTab as $index => $data) {

    $totalRow;
    if ($data['href'] === 'goods-p.php') {
        $totalRow = $SOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-pending-p.php') {
        $totalRow = $pendingSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-approved-p.php') {
        $totalRow = $approvedSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-exceptional-p.php') {
        $totalRow = $exceptionalSOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-sales-orders-item-wise-p.php') {
        $totalRow = $SOItemWiseCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-job-order-pending-list-p.php') {
        $totalRow = $pendingJOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-job-order-list-p.php') {
        $totalRow = $openJOCountObj['rowCount'];
    }

    $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";


?>

<style>
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
</style>

    <div class="page-list-filer">
        <a href="<?= $data['href'] ?>" class="filter-link <?= $activeClassName ?>"><ion-icon name="list-outline"></ion-icon><?= $data['name'] ?>
        </a>
    </div>
<?php } ?>

<script>
    $(document).ready(function() {
        $('.filter-link').on('click', function(e) {
            $('.filter-link').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>