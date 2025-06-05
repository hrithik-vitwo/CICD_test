<?php
// require_once("../../../app/v1/connection-branch-admin.php");
function soDelete($tableName, $soNum)
{
    global $dbCon;
    $table_name = $tableName;
    $so_no = $soNum;

    $sql = "UPDATE `" . $table_name . "` SET `status`='deleted' WHERE `so_number`='" . $so_no . "' ";
    if (mysqli_query($dbCon, $sql)) {
        $returnData['status'] = "success";
        $returnData['message'] = "Deleted successfully";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Deleted failed";
    }

    return $returnData;
}
