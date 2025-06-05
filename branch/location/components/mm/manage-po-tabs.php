<?php
$soTab = [
    [
        'name' => 'All',
        'href' => 'manage-purchases-orders.php'
    ],
    [
        'name' => 'Item Order List',
        'href' => 'po-items.php'
    ]
    // [
    //     'name' => 'Pending PO',
    //     'href' => 'manage-purchases-orders-pending-p.php'
    // ],
    // [
    //     'name' => 'Open PO',
    //     'href' => 'manage-purchases-orders-open-p.php'
    // ],
    // [
    //     'name' => 'Closed PO',
    //     'href' => 'manage-purchases-orders-closed-p.php'
    // ],
    // [
    //     'name' => 'Service PO',
    //     'href' => 'manage-purchases-orders-service-p.php'
    // ]
];



foreach ($soTab as $index => $data) {
    $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";
?>

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