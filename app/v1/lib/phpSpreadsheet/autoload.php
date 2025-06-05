<?php
require_once('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

function readTheXlsxAndCsvFile($fileObj = [])
{
    $fileName = $fileObj["name"] ?? "";
    $fileTempName = $fileObj["tmp_name"] ?? "";

    $arr_file = explode('.', strtolower($fileName));
    $extension = end($arr_file);
    if (!in_array($extension, ["csv", "xls", "xlsx"])) {
        return [
            "status" => "warning",
            "message" => "Invalid File format, please provide only csv, xlsx, xls format",
            "data" => []
        ];
    }
    if ('csv' == $extension) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
    } else {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    }
    $spreadsheet = $reader->load($fileTempName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    if (count($sheetData) > 0) {
        return [
            "status" => "success",
            "message" => "Successfully read the file",
            "data" => $sheetData
        ];
    } else {
        return [
            "status" => "warning",
            "message" => "Data not found in selected sheet",
            "data" => []
        ];
    }
}