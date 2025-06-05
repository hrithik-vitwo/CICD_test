<?php
$soTab = [
    [
        'name' => 'Concised View',
        'href' => 'manage-mrp-concised-p.php'
    ],
    [
        'name' => 'Detailed View',
        'href' => 'manage-mrp-detailed-p.php'
    ]
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