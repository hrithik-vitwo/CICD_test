<?php
require_once("../../app/v1/connection-branch-admin.php");

// preview.php

// Check if a file was submitted
if (isset($_FILES['excelFile'])) {

    //$file = $_FILES['excelFile']['tmp_name'];
    $excel =  readTheXlsxAndCsvFile($_FILES['excelFile']);
    // console($excel);
    http_response_code(200);
    echo '<table id="previewTable">';
    foreach($excel['data'] as $row){

        if (empty(array_filter($row))) {
            continue;
        }
        
        echo '<tr>';
        foreach($row as $cell){
            ?>
            <td><?= $cell ?></td>
            <?php
        }
        echo '</tr>';
    }
    echo '</table>';
}else {
    http_response_code(400);
    echo "No file submitted.";
}
?>
