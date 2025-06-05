<?php
class MRPController
{
    function addMrpGroup($POST)
    {
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $isValidate = validate($POST, [
            "mrpGroupName" => "required",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $mrpGroupName = $POST['mrpGroupName'];
        // console($POST);
        $insert = "INSERT INTO `erp_customer_mrp_group`
					SET
					`customer_mrp_group` = '" . $mrpGroupName . "', 
                    `branch_id`=$branch_id,
                    `company_id`=$company_id,
                    `location_id`=$location_id,                           
                    `created_by`='" . $created_by . "',
                    `updated_by`='" . $updated_by . "'                            
                    ";


        $res = queryInsert($insert);
        return $res;
    }



    function editMrpGroup($POST)
    {
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $isValidate = validate($POST, [
            "mrpGroupName" => "required",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $mrpGroupName = $POST['mrpGroupName'];
        // console($POST);
        $sql = "UPDATE  `erp_customer_mrp_group`
					SET
					`customer_mrp_group` = '" . $mrpGroupName . "'
                    WHERE `customer_mrp_group_id`='" . $POST['id'] . "' AND `company_id`=$company_id  AND `branch_id`=$branch_id AND `location_id`=$location_id
                          
                    ";


        $res = queryUpdate($sql);
        return $res;
    }
}
