<?php
require_once("../../app/v1/connection-branch-admin.php");
function excelExport($sql){
    // $sql = "SELECT * FROM erp_proforma_invoices";
    // $result = $conn->query($sql);
    $result=queryGet($sql)['data'];

    if ($result['numRows'] > 0) {
        // Create a new Excel file
        $excelFile = 'data_export.xlsx';
        $spreadsheet = new COM("Excel.Application") or die("Unable to instantiate Excel");
        $spreadsheet->DisplayAlerts = false;
        $workbook = $spreadsheet->Workbooks->Add();
        $sheet = $workbook->Worksheets(1);

        // Write headers
        $columnHeaders = $result->fetch_fields();
        $columnIndex = 1;
        foreach ($columnHeaders as $column) {
            $sheet->Cells(1, $columnIndex)->Value = $column->name;
            $columnIndex++;
        }

        // Write data rows
        $row = 2;
        while ($row_data = $result->fetch_assoc()) {
            $columnIndex = 1;
            foreach ($row_data as $value) {
                $sheet->Cells($row, $columnIndex)->Value = $value;
                $columnIndex++;
            }
            $row++;
        }

        // Save and download the Excel file
        $workbook->SaveAs($excelFile);
        $workbook->Close();
        $spreadsheet->Quit();
        unset($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="data_export.xlsx"');
        header('Cache-Control: max-age=0');
        readfile($excelFile);
        unlink($excelFile); // Remove the temporary Excel file
        exit();
    } else {
        echo "No data found.";
    }
}
?>
