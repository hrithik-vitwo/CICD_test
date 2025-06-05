<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$filename = 'users.csv';

// $tableData = $_POST['export_data'];
$tableData = [
    [
        "name" => "Rachhel Sekh",
        "age" => "25",
        "email" => "rachhel@vitwo.in"
    ]
];

$export_data = $tableData;
// file creation //
$file = fopen($filename, "w");
foreach ($export_data as $line) {
    fputcsv($file, $line);
}

fclose($file);
//download//

header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Type: application/csv; ");
readfile($filename);
// deleting file
unlink($filename);
exit();

