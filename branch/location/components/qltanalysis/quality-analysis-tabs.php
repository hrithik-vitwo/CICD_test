<?php
$soTab = [
    [
        'name' => 'All',
        'href' => 'recieved-item-p.php'
    ],
    [
        'name' => 'FG',
        'href' => 'recieved-item-fg-p.php'
    ],
    [
        'name' => 'SFG',
        'href' => 'recieved-item-sfg-p.php'
    ],
    [
        'name' => 'RM',
        'href' => 'recieved-item-rm-p.php'
    ],
    [
        'name' => 'Rejected',
        'href' => 'recieved-item-rejected-p.php'
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