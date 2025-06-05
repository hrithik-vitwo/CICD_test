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
    $rejected_value = $_POST["reject_value"];
    $all_link = $_POST["all_link"];
    $stock_id = $_POST["stock_id"];
    $status = $_POST["status"];
    $received_qty = $_POST["received_qty"];
    $doc_no = "QA".time();


    if (isset($_FILES["file"]) && $_FILES["file"] != "") {
        $file_tmpname = $_FILES["file"]["tmp_name"];
        $file_name = $_FILES["file"]['name'];
        $file_size = $_FILES["file"]['size'];

        $allowed_types = ['pdf'];
        $maxsize = 2 * 1024 * 1024; // 10 MB

        $uploadedFileObj = uploadFile(["name" => $file_name, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);

        if ($uploadedFileObj["status"] == "success") {
            $uploadedFileName = $uploadedFileObj["data"];
        } else {
            $uploadedFileName = "";
        }
    } else {
        $uploadedFileName = "";
    }



    $check_summary = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'",false);

    if($check_summary["numRows"] == 0)
    {
        //Insert
        $sql="INSERT INTO `erp_qa_summary` 
        SET 
        `companyId`='$company_id',
        `branchId`='$branch_id',
        `locationId`='$location_id',
        `stock_log_id`='$stock_id',
        `status`='$status',
        `qa_file`='$uploadedFileName',
        `passed`='$passed_value',
        `rejected`='$rejected_value',
        `qaCreatedBy`='$created_by',
        `qaUpdatedBy`='$updated_by'";

        queryInsert($sql);
    }
    else
    {
        $passed = $check_summary["data"]["passed"];
        $rejected = $check_summary["data"]["rejected"];


        $total_passed = $passed + $passed_value;
        $total_rejected = $rejected + $rejected_value;
        //Update
        $update = "UPDATE `erp_qa_summary` SET `status`='".$status."',`passed`='".$total_passed."',`rejected`='".$total_rejected."',`qa_file`='".$uploadedFileName."',`qaUpdatedBy`='".$updated_by."' WHERE `stock_log_id`='".$stock_id."'";
        $updateObj = queryUpdate($update);
    }

    //Insert in LOG
    $log="INSERT INTO `erp_qa_log` 
        SET 
        `companyId`='$company_id',
        `branchId`='$branch_id',
        `locationId`='$location_id',
        `stock_log_id`='$stock_id',
        `doc_no`='$doc_no',
        `qa_file`='$uploadedFileName',
        `status`='$status',
        `passed`='$passed_value',
        `rejected`='$rejected_value',
        `qaCreatedBy`='$created_by',
        `qaUpdatedBy`='$updated_by'";

        $insert = queryInsert($log);

        $inserted_id = $insert["insertedId"];

        if(count($all_link) > 0)
        {
            foreach($all_link as $data)
            {
                $link="INSERT INTO `erp_qa_link` 
                        SET 
                        `companyId`='$company_id',
                        `branchId`='$branch_id',
                        `locationId`='$location_id',
                        `stock_log_id`='$stock_id',
                        `qa_log_Id`='$inserted_id',
                        `link`='$data',
                        `qaCreatedBy`='$created_by',
                        `qaUpdatedBy`='$updated_by'";

                        queryInsert($link);
        
            }
        }

        //Insert into stock log

        if($passed_value > 0)
        {

        $get_stock = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stockLogId`='$stock_id'",false);

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