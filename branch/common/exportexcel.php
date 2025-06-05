<?php
include_once("../../app/v1/connection-branch-admin.php");

function exportToExcelAll($sql, $column,$isStock=0)
{
    $columnMapping = json_decode($column, true);
    $slags = [];
    $sqlcond = "";
    $sqlMainQryObj = "";
    $dbObj = new Database();

    foreach ($columnMapping as $col) {
        if ($col['slag'] !== 'sl_no') {
            $slags[] = $col['slag'];
        }
    }
    $selectClause = implode(",", $slags);

    if (preg_match('/SET\s+sql_mode\s*=\s*\(\s*SELECT\s+REPLACE\s*\(\s*@@sql_mode\s*,\s*\'ONLY_FULL_GROUP_BY\'\s*,\s*\'\'\s*\)\s*\);/i', $sql)) {

        $sql = preg_replace('/SET\s+sql_mode\s*=\s*\(\s*SELECT\s+REPLACE\s*\(\s*@@sql_mode\s*,\s*\'ONLY_FULL_GROUP_BY\'\s*,\s*\'\'\s*\)\s*\);/i', '', $sql);
        $fromPos = stripos(trim($sql), 'item.itemCode,');
        $newSql = substr($sql, $fromPos);
        $sqlcond .= "SELECT " . $newSql;
        // $sqlcond .= $sql;
        $dbObj->queryUpdate("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))", true);
    } else {

        $fromPos = stripos(trim($sql), 'FROM');
        $newSql = substr($sql, $fromPos);
        $sqlcond .= "SELECT " . $selectClause . " " . $newSql;
    }

    if($isStock=1){
        $sqlMainQryObj =  $dbObj->queryGet($sql, true);
    }else{
        $sqlMainQryObj =  $dbObj->queryGet($sqlcond, true);

    }
    $result = $sqlMainQryObj['data'];
    ob_start();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_export.csv"');

    $output = fopen('php://output', 'w');

    $headerRow = [];
    foreach ($columnMapping as $column) {
        $headerRow[] = $column['name'];
    }
    fputcsv($output, $headerRow);
    $sl_no = 0;

    foreach ($result as $data) {
        $sl_no++;
        array_unshift($data, $sl_no);
        fputcsv($output, $data);
    }

    fclose($output);
    $csvContent = ob_get_clean();
// return ["sql"=>$sql,"condSql"=>$sqlMainQryObj['query'],"columns"=>$columnMapping];
    return $csvContent;
}


function exportToExcelByPagin($sql, $column)
{
    $columnMapping = json_decode($column, true);
    $slags = [];
    $sqlcond = "";
    $sqlMainQryObj = "";
    $dbObj = new Database();

    foreach ($columnMapping as $col) {
        if ($col['slag'] !== 'sl_no') {
            $slags[] = $col['slag'];
        }
    }
    $selectClause = implode(",", $slags);

    if (preg_match('/SET\s+sql_mode\s*=\s*\(\s*SELECT\s+REPLACE\s*\(\s*@@sql_mode\s*,\s*\'ONLY_FULL_GROUP_BY\'\s*,\s*\'\'\s*\)\s*\);/i', $sql)) {

        $sql = preg_replace('/SET\s+sql_mode\s*=\s*\(\s*SELECT\s+REPLACE\s*\(\s*@@sql_mode\s*,\s*\'ONLY_FULL_GROUP_BY\'\s*,\s*\'\'\s*\)\s*\);/i', '', $sql);
        $fromPos = stripos(trim($sql), 'item.itemCode,');
        $newSql = substr($sql, $fromPos);
        $sqlcond .= "SELECT " . $newSql;
        // $sqlcond .= $sql;
        $dbObj->queryUpdate("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))", true);
    } else {

        $fromPos = stripos(trim($sql), 'FROM');
        $newSql = substr($sql, $fromPos);
        $sqlcond .= "SELECT " . $selectClause . " " . $newSql;
    }


    $sqlMainQryObj =  $dbObj->queryGet($sqlcond, true);
    $result = $sqlMainQryObj['data'];
    ob_start();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_export.csv"');

    $output = fopen('php://output', 'w');

    $headerRow = [];
    foreach ($columnMapping as $column) {
        if ($column['slag'] !== 'sl_no') {
        $headerRow[] = $column['name'];
        }
    }
    fputcsv($output, $headerRow);
    $sl_no = 0;

    foreach ($result as $data) {
        $sl_no++;
        // array_unshift($data, $sl_no);
        fputcsv($output, $data);
    }

    fclose($output);
    $csvContent = ob_get_clean();
    return $csvContent;
}
