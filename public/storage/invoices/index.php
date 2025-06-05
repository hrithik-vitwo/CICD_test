<?php

$path    = '.';
$files = scandir($path);

foreach($files as $file){
    if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) == "pdf"){
        ?>
        <ul>
            <li><a href="<?= $file ?>"><?= $file ?></a></li>
        </ul>
        <?php
    }
}

?>