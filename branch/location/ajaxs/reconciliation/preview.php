<?php

require_once("../../../../app/v1/connection-branch-admin.php");


if($_GET['type'] == "reconcile"){

     $id = $_GET['id'];

   $get =  queryGet("SELECT * FROM `erp_reconciliation` WHERE `id`=$id ");

 //  console($get);

    
 $selected_csv = $get['data']['files']; // Replace this with the actual filename you want to display

//Read CSV file and convert to an array
$csv_data = array_map('str_getcsv', file(COMP_STORAGE_URL . "/others/" . $selected_csv));



// Display the CSV data in a table format
echo "<input type='hidden' id='id' value=".$id.">";
echo "<table border='1' id='previewTable'>";
foreach ($csv_data as $row) {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<td>" . htmlspecialchars($cell) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

}


?>
