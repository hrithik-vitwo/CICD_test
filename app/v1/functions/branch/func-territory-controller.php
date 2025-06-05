<?php
class TerritoryController
{
    function addTerritory($POST)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        $returnData = [];

        $isValidate = validate($POST, [
            "territoryname" => "required",
            "stateCode" => "required"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $stateCode = serialize($POST["stateCode"]);
        $territoryname = $POST['territoryname'];
        $inSql = "INSERT INTO `erp_mrp_territory` 
                SET
            `territory_name`='" . $territoryname . "',
            `state_codes`='" . $stateCode . "',
            `update_by`='" . $created_by . "',
            `created_by`='" . $created_by . "',
            `company_id`=$company_id,
    		`branch_id`=$branch_id,
    		`location_id`=$location_id            
            ";

        $insert = queryInsert($inSql);
   

        if ($insert['status'] == 'success') {
            $returnData['status'] = 'success';
            $returnData['message'] = 'Inserted Successfully';
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'something went wrong';
        }

        return $returnData;
    }

 function editTerritory($POST)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        $returnData = [];

        $isValidate = validate($POST, [
            "territoryname" => "required",
            "stateCode" => "required"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $stateCode = serialize($POST["stateCode"]);
        $territoryname = $POST['territoryname'];
 		$territoryId=$POST['territoryid'];
        $upSql = "UPDATE`erp_mrp_territory` 
                SET
            `territory_name`='" . $territoryname . "',
            `state_codes`='" . $stateCode . "',
            `update_by`='" . $created_by . "'
            WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `territory_id`=$territoryId          
            ";

        $insert = queryUpdate($upSql);
   

        if ($insert['status'] == 'success') {
            $returnData['status'] = 'success';
            $returnData['message'] = 'Updated Successfully';
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'something went wrong';
        }

        return $returnData;
    }


// end of class
}
