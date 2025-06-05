<?php
class KAMController
{

    function createKam($POST, $branch_id, $company_id, $location_id, $created_by)
    {
        // console($branch_id);


        $isValidate = validate($POST, [
            "name" => "required",
            "description" => "required",
      
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        // $lastQuery = "SELECT * FROM `" . ERP_WAREHOUSE . "` ORDER BY `warehouse_id` DESC LIMIT 1";
        // $last = queryGet($lastQuery);
        // $lastRow = $last['data'] ?? "";
        // $lastid = $lastRow['warehouse_code'] ?? ""; 
        // $returnWarehouseCode = getWHSerialNumber($lastid);
        

        //getWHSerialNumber($lastsl)
        $p_id = $POST["p_id"] ?? 0;
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["description"] = $POST["description"];
        $admin["p_id"] = $p_id;

        $status = $POST["createKam"] == "add_draft" ? "draft" : "active";

  $ins = "INSERT INTO `" . ERP_KAM . "` 
        SET
            `kamName`='" .  $admin["name"] . "',
            `description`='" . $admin["description"] . "',
            `parentId`='" . $admin["p_id"] . "',
            `company_id`='" .  $company_id  . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "',
            `kamCode`= 1, 
            `created_by`='" . $created_by . "' "; 
        $insertItem = queryInsert($ins);
        //console($location_id);
        //exit();
        $returnData = $insertItem;
        return $returnData;
    }

    function editKam($POST, $branch_id, $company_id, $location_id, $created_by)
    {
        // console($branch_id);


        $isValidate = validate($POST, [
            "name" => "required",
           
            "description" => "required",
         
           

      
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        // $lastQuery = "SELECT * FROM `" . ERP_WAREHOUSE . "` ORDER BY `warehouse_id` DESC LIMIT 1";
        // $last = queryGet($lastQuery);
        // $lastRow = $last['data'] ?? "";
        // $lastid = $lastRow['warehouse_code'] ?? "";
        // $returnWarehouseCode = getWHSerialNumber($lastid);
        

        //getWHSerialNumber($lastsl)
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["description"] = $POST["description"];
        $admin["p_id"] = $POST["p_id"];
       $kam_id = $POST['kam_id'];

        $status = $POST["editKam"] == "add_draft" ? "draft" : "active";

    $ins = "UPDATE `" . ERP_KAM . "` 
        SET
            `kamName`='" .  $admin["name"] . "',
            `description`='" . $admin["description"] . "',
            `parentId`='" . $admin["p_id"] . "',
            `company_id`='" .  $company_id  . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "',
            `created_by`='" . $created_by . "'
            WHERE `kamId`='".$kam_id."'
            ";
        $insertItem = queryUpdate($ins);
        $returnData = $insertItem;
        return $returnData;
    }
    
   
}
