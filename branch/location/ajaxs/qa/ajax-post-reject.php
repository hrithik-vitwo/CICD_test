<?php
require_once("../../../../app/v1/connection-branch-admin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $passed_value = $_POST["passed_value"];
    $qa_log_id = $_POST["qa_log_id"];
    $remarks = $_POST["remarks"];

    $log = queryGet("SELECT * FROM `erp_qa_log` WHERE `qa_log_Id` = '$qa_log_id'",false);

    $stock_log_id = $log["data"]["stock_log_id"];

    $passed_value_db = $log["data"]["passed"];
    $rejected_value_db = $log["data"]["rejected"];

    $updated_passed_value = $passed_value_db + $passed_value;
    $updated_rejected_value = $rejected_value_db - $passed_value;


    //Update QA Log
    $update_log = "UPDATE `erp_qa_log` SET `passed`='".$updated_passed_value."',`rejected`='".$updated_rejected_value."',`remarks`='".$remarks."',`qaUpdatedBy`='".$updated_by."' WHERE `qa_log_Id`='".$qa_log_id."'";
    $updateLogObj = queryUpdate($update_log);


    $stock_summary = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_log_id'",false);


    $passed = $stock_summary["data"]["passed"];
    $rejected = $stock_summary["data"]["rejected"];


    $total_passed = $passed + $passed_value;
    $total_rejected = $rejected - $passed_value;
    //Update
    $update_summary = "UPDATE `erp_qa_summary` SET `passed`='".$total_passed."',`rejected`='".$total_rejected."',`qaUpdatedBy`='".$updated_by."' WHERE `stock_log_id`='".$stock_log_id."'";
    $update_summary_Obj = queryUpdate($update_summary);



        //Insert into stock log

        if($passed_value > 0)
        {

        $get_stock = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stockLogId`='$stock_log_id'",false);

        $storageLocationId = $get_stock["data"]["storageLocationId"];
        $storageType = $get_stock["data"]["storageType"];
        $itemId = $get_stock["data"]["itemId"];
        $itemQty = $passed_value * (-1);
        $positive_qty = $passed_value;
        $itemUom = $get_stock["data"]["itemUom"];
        $itemPrice = $get_stock["data"]["itemPrice"];
        $refActivityName = $get_stock["data"]["refActivityName"];
        $logRef = $get_stock["data"]["logRef"];
        $refNumber = $get_stock["data"]["refNumber"];
        $min_stock = $get_stock["data"]["min_stock"];
        $max_stock = $get_stock["data"]["max_stock"];
        $bornDate = $get_stock["data"]["bornDate"];
        $postingDate = date("Y-m-d");

        $stock_log_query = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="'.$storageType.'",`storageLocationId`=' . $storageLocationId . ', `itemId`=' . $itemId . ',`itemQty`=' . $itemQty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $itemPrice . ', `refActivityName`="QC", `logRef`="' . $logRef . '", `refNumber`="' . $refNumber . '", `bornDate`="' . $bornDate . '", `postingDate`="' . $postingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

        queryInsert($stock_log_query);

        //get default storage location
        $summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$itemId' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'",false);
        $default_storage_location = $summary["data"]["default_storage_location"];

        $get_storage_loc_details = queryGet("SELECT * FROM `erp_storage_location` WHERE `storage_location_id`='$default_storage_location'",false);
        $defaultstorageType = $get_storage_loc_details["data"]["storageLocationTypeSlug"];

        $default_stock_log_query = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="'.$defaultstorageType.'",`storageLocationId`=' . $default_storage_location . ', `itemId`=' . $itemId . ',`itemQty`=' . $positive_qty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $itemPrice . ', `refActivityName`="QC", `logRef`="' . $logRef . '", `refNumber`="' . $refNumber . '", `bornDate`="' . $bornDate . '", `postingDate`="' . $postingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

         queryInsert($default_stock_log_query);
        }
    
  $returnData = [
        "status" => "success",
        "message" => "Data saved successfully"
    ];
    echo json_encode($returnData);

}

?>