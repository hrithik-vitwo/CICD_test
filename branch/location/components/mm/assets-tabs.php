<?php
$tab = [
    [
        'name' => 'All Assets',
        'href' => 'manage-assets.php'
    ],
    [
        'name' => 'Assets in use',
        'href' => 'manage-assets-inuse.php'
    ],
    [
        'name' => 'FAR',
        'href' => 'far.php'
    ],
    [
        'name' => 'Assets under construction',
        'href' => 'manage-assets.php?assetC'
    ],
    

];



foreach ($tab as $index => $data) {

    $totalRow;
    if ($data['href'] === 'manage-assets-p.php') {
        $totalRow = $SOCountObj['rowCount'];
    } elseif ($data['href'] === 'manage-assets-p.php?asset') {
        $totalRow = $pendingSOCountObj['rowCount'];
    } elseif ($data['href'] === 'assets-under-construction.php') {
        $totalRow = $approvedSOCountObj['rowCount'];
    }
    $queryString = $_SERVER['QUERY_STRING'];
    if($queryString=="assetC" && $data['href']=="manage-assets.php?assetC")
    {
        $activeClassName="active";
    }
    if($queryString=="asset")
    {
        $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";
    }
    if(empty($queryString)){ 
        $activeClassName = basename($_SERVER['PHP_SELF']) == $data['href'] ? "active" : "";
   
    }

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