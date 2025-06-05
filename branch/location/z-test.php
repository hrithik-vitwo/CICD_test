<?php
require_once("../../app/v1/connection-branch-admin.php");

$file = fopen("contacts.csv", "r");
// $dbObj = new Database();


$LineStart = 0;
$LineStop = 10;
$LineNo = 0;
while ($LineNo <= $LineStop) {
    $LineNo++;
    if ($LineNo >= $LineStart && $LineNo <= $LineStop) {
        $row = fgetcsv($file);
        $CircleName = $row[0];
        $RegionName = $row[1];
        $DivisionName = $row[2];
        $OfficeName = $row[3];
        $Pincode = $row[4];
        $OfficeType = $row[5];
        $Delivery = $row[6];
        $District = $row[7];
        $StateName = $row[8];
        $Latitude = $row[9];
        $Longitude = $row[10];

        console($row);

        // $dbObj->queryInsert("INSERT INTO `erp_master_pin_india` SET `CircleName`=$CircleName,`RegionName`=$RegionName,`DivisionName`=$DivisionName,`OfficeName`=$OfficeName,`Pincode`=$Pincode,`OfficeType`=$OfficeType,`Delivery`=$Delivery,`District`=$District,`StateName`=$StateName,`Latitude`=$Latitude,`Longitude`=$Longitude");
    }
}
fclose($file);
