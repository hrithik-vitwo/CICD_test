<?php
include_once("../../app/v1/connection-branch-admin.php");
session_start();
header('Content-Type: application/json');
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if ($_POST['act'] == "paginationlist") {
        $coloum=$_POST['coloum'];
        $data=$_POST['data'];
        $sql_data_checkbox=$_POST['sql_data_checkbox'];
        $exportResult = exportToExcelByPagin($data, $coloum, $sql_data_checkbox);

        // Send JSON response to indicate success
        echo json_encode([
            'status' => 'success',
            'message' => 'CSV generated',
            'csvContentpage' => $exportResult // Encoding CSV content to handle safely in JSON
        ]);
    }
    elseif($_POST['act'] == "fullliststock"){
        $coloum=$_POST['coloum'];
        $data=$_POST['data'];
        $sql_data_checkbox=$_POST['sql_data_checkbox'];
        $exportResult = exportToExcelAll($data, $coloum, $sql_data_checkbox);
        // Send JSON response to indicate success
        echo json_encode([
            'status' => 'success',
            'message' => 'CSV allgenerated',
            'csvContentall' => $exportResult // Encoding CSV content to handle safely in JSON
        ]);
    }elseif($_POST['act'] == "paginationliststock"){
        $data=$_POST['data'];
        $exportResult = exportToCSV($data);

        // Send JSON response to indicate success
        echo json_encode([
            'status' => 'success',
            'message' => 'CSV generated',
            'csvContentpage' => $exportResult // Encoding CSV content to handle safely in JSON
        ]);
    }
    
}
function exportToExcelAll($dynmaicdataall, $column, $sql_data_checkbox)
{
    $filteredSlags='';
    $dynmaicdataall = json_decode($dynmaicdataall, true);
    $columnMapping = $column;
    $sql_data_checkbox= json_decode($sql_data_checkbox, true);
    if (empty($sql_data_checkbox)) {
        $filteredSlags = array_slice(array_column($columnMapping, 'slag'), 0, 5);
    } else {
        $filteredSlags = array_intersect($sql_data_checkbox, array_column($columnMapping, 'slag'));
    }
    
    $slags = [];
    $headerRow = ['SL No'];
    $filteredSlag = array_intersect($filteredSlags, array_column($columnMapping, 'slag'));
    foreach ($columnMapping as $col) {
        if (in_array($col['slag'], $filteredSlag) && $col['slag'] !== 'sl_no') {
            $slags[] = $col['slag'];
            $headerRow[] = $col['name'];
        }
    }


    ob_start();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_download.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, $headerRow);
    foreach ($dynmaicdataall as $index => $data) {
        $row = [$index + 1]; 
        foreach ($slags as $slag) {
            if (isset($data[$slag])) {
                $row[] = $data[$slag];    
            } else {
                $row[] = '';
            }
        }
        fputcsv($output, $row);
    }
    fclose($output);
    $csvContent = ob_get_clean();

    return $csvContent;
}



function exportToExcelByPagin($dynmaicdata, $column, $sql_data_checkbox)
{ 
    $filteredSlags='';
    $dynmaicdata = json_decode($dynmaicdata, true);
    $columnMapping = $column;
    $sql_data_checkbox= json_decode($sql_data_checkbox, true);
    if (empty($sql_data_checkbox)) {
        $filteredSlags = array_slice(array_column($columnMapping, 'slag'), 0, 5);
    } else {
        $filteredSlags = array_intersect($sql_data_checkbox, array_column($columnMapping, 'slag'));
    }
    $slags = [];
    $headerRow = [];  
    $filteredSlag = array_intersect($filteredSlags, array_column($columnMapping, 'slag'));
    foreach ($columnMapping as $col) {
        if (in_array($col['slag'], $filteredSlag) || $col['slag'] === 'sl_no') {
            $slags[] = $col['slag'];
            $headerRow[] = $col['name'];
        }
    }

    ob_start();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, $headerRow);
    foreach ($dynmaicdata as $index => $data) {
        $row = [];
        foreach ($slags as $slag) {
            if (isset($data[$slag])) {    
                    $row[] = $data[$slag];  
            } else {
                $row[] = '';  
            }
        }
        fputcsv($output, $row);
    }

    fclose($output);
    $csvContent = ob_get_clean();
    return $csvContent;
}

function exportToCSV($data)
{
    $data = json_decode($data, true);

    if (empty($data) || !is_array($data)) {
        return '';
    }

    ob_start();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_export.csv"');

    $output = fopen('php://output', 'w');

    // Extract header from the first row keys
    $header = array_keys(reset($data));
    fputcsv($output, $header);

    // Write rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    return ob_get_clean();
}

function downloadstock($dynmaicdataall)
{
    $dynmaicdataall = json_decode($dynmaicdataall, true);

    if (empty($dynmaicdataall) || !is_array($dynmaicdataall)) {
        return '';
    }

    ob_start();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_download.csv"');

    $output = fopen('php://output', 'w');

    // Get all headers from first row
    $headers = array_keys(reset($dynmaicdataall));
    array_unshift($headers, 'SL No');
    fputcsv($output, $headers);

    foreach ($dynmaicdataall as $index => $data) {
        $row = array_values($data);
        array_unshift($row, $index + 1); // Add SL No
        fputcsv($output, $row);
    }

    fclose($output);
    return ob_get_clean();
}


