<?php

$soTab = [
    [
        'name' => 'Payroll',
        'href' => 'payroll.php'
    ],
    [
        'name' => 'Salary',
        'href' => 'salary.php'
    ],
    [
        'name' => 'TDS',
        'href' => 'tds.php'
    ],
    [
        'name' => 'ESI',
        'href' => 'esi.php'
    ],
    [
        'name' => 'PF',
        'href' => 'pf.php'
    ],
    [
        'name' => 'P-TAX',
        'href' => 'ptax.php'
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