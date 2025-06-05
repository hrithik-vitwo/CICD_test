<?php
require_once("../../../../app/v1/connection-branch-admin.php");

// preview.php

// Check if a file was submitted
if (isset($_FILES['excelBatchFile'])) {

    //$file = $_FILES['excelFile']['tmp_name'];
    $excel =  readTheXlsxAndCsvFile($_FILES['excelBatchFile']);
    // console($excel['data']);

    http_response_code(200);
    echo '<table id="previewBatchTable">';
    $dataWithoutHeader = array_slice($excel['data'], 1);
    //console($dataWithoutHeader);
    $sum = 0;
    $physicalqty = 0;

    // Loop through the array and sum up the 5th index of each sub-array
    foreach ($dataWithoutHeader as $subArray) {
        if (isset($subArray[5])) {
            $sum += $subArray[5];
            
        }
        if (isset($subArray[9])) {
            $physicalqty += $subArray[9];
            
        }

    }
    $diff = $sum - $physicalqty;
    echo "Total Qtuantity: " . $sum."<br>";
    echo "Total Physical Quantity: " . $physicalqty."<br>";
    echo "Total Difference: " . $diff."<br>";
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
