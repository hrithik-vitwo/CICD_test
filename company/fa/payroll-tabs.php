<?php

$soTab = [
    [
        'name' => 'Payroll',
        'href' => 'payroll-p.php'
    ],
    [
        'name' => 'Salary',
        'href' => 'salary-p.php'
    ],
    [
        'name' => 'TDS',
        'href' => 'tds-p.php'
    ],
    [
        'name' => 'ESI',
        'href' => 'esi-p.php'
    ],
    [
        'name' => 'PF',
        'href' => 'pf-p.php'
    ],
    [
        'name' => 'P-TAX',
        'href' => 'ptax-p.php'
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