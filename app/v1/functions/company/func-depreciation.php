<?php



function importdep($POST)
{

    global $company_id;
    global $created_by;




    $returnData = [];
    $isValidate = validate($POST, [
        "rule_type" => "required",
        "rule_subtype" => "required",
        "schedule" => "required",

    ], [
        "rule_type" => "Enter Rule Type",
        "rule_subtype" => "Enter Rule subtype",
        "schedule" => "Enter Schedule",
    ]);




    if ($isValidate["status"] == "success") {

        $alreadyexist = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`= '" . $company_id . "'", true);
        if ($alreadyexist['numRows'] > 0) {
            $returnData['status'] = "warning";
            $returnData['message'] = "A rule has already been imported. You cannot import a different rule now !";
            return $returnData;
        }

        $rule = $POST["rule_type"];
        $rulesub = $POST["rule_subtype"];
        $schedule = $POST['schedule'];

        if ($rule == "it") {
            $getkey = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`= 0 AND `rule_type`= 'IT' ", true);
            //  console($getkey);
            if ($getkey['numRows'] > 0) {
                foreach ($getkey['data'] as $data) {

                    console($data);


                    $desp_key = $data['desp_key'];
                    $asset_class = $data['asset_class'];
                    $parent_code = $data['parent_code'];
                    $asset_life = $data['asset_life'];
                    $wdv = $data['wdv'];
                    $slm = $data['slm'];
                    $disposable_value = $data['disposable_value'];
                    $rule_type = $data['rule_type'];

                    $insert = queryInsert("INSERT INTO `erp_depreciation_table` SET `desp_key`='" . $desp_key . "',`asset_class`='" . $asset_class . "',`parent_code`='" . $parent_code . "',`asset_life`='" . $asset_life . "',`wdv`='" . $wdv . "',`slm`='" . $slm . "',`disposable_value`='" . $disposable_value . "',`rule_type`='" . $rule_type . "',`company_id`=$company_id,`created_by`='" . $created_by . "',`updated_by`='" . $created_by . "'");
                    //console($insert);




                }
                if ($insert['status'] == "success") {
                    $update = queryUpdate("UPDATE `erp_companies` SET `depreciation_type`='" . $rulesub . "',`depreciation_schedule`='" . $schedule . "' WHERE `company_id` = $company_id");

                    $returnData['status'] = "success";
                    $returnData['message'] = "Imported successfully";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Something went wrong";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "IT Rule Data Not Found !";
            }





            return $returnData;
        } elseif ($rule == "management") {

            $getkey = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`= 0 AND `rule_type`= 'Company' ", true);
            foreach ($getkey['data'] as $data) {

                $desp_key = $data['desp_key'];
                $asset_class = $data['asset_class'];
                $parent_code = $data['parent_code'];
                $asset_life = $data['asset_life'];
                $wdv = $data['wdv'];
                $slm = $data['slm'];
                $disposable_value = $data['disposable_value'];
                $rule_type = "management";

                $insert = queryInsert("INSERT INTO `erp_depreciation_table` SET `desp_key`='" . $desp_key . "',`asset_class`='" . $asset_class . "',`parent_code`='" . $parent_code . "',`asset_life`='" . $asset_life . "',`wdv`='" . $wdv . "',`slm`='" . $slm . "',`disposable_value`='" . $disposable_value . "',`rule_type`='" . $rule_type . "',`company_id`=$company_id,`created_by`='" . $created_by . "',`updated_by`='" . $created_by . "'");
            }




            if ($insert['status'] == "success") {
                $update = queryUpdate("UPDATE `erp_companies` SET `depreciation_type`='" . $rulesub . "',`depreciation_schedule`='" . $schedule . "' WHERE `company_id` = $company_id");

                $returnData['status'] = "success";
                $returnData['message'] = "Imported successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Something went wrong";
            }

            return $returnData;
        } else {

            $getkey = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`= 0 AND `rule_type`= 'Company' ", true);
            foreach ($getkey['data'] as $data) {

                $desp_key = $data['desp_key'];
                $asset_class = $data['asset_class'];
                $parent_code = $data['parent_code'];
                $asset_life = $data['asset_life'];
                $wdv = $data['wdv'];
                $slm = $data['slm'];
                $disposable_value = $data['disposable_value'];
                $rule_type = $data['rule_type'];

                $insert = queryInsert("INSERT INTO `erp_depreciation_table` SET `desp_key`='" . $desp_key . "',`asset_class`='" . $asset_class . "',`parent_code`='" . $parent_code . "',`asset_life`='" . $asset_life . "',`wdv`='" . $wdv . "',`slm`='" . $slm . "',`disposable_value`='" . $disposable_value . "',`rule_type`='" . $rule_type . "',`company_id`=$company_id,`created_by`='" . $created_by . "',`updated_by`='" . $created_by . "'");
            }

            


            if ($insert['status'] == "success") {
                $update = queryUpdate("UPDATE `erp_companies` SET `depreciation_type`='" . $rulesub . "',`depreciation_schedule`='" . $schedule . "' WHERE `company_id` = $company_id");

                $returnData['status'] = "success";
                $returnData['message'] = "Imported successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Something went wrong";
            }

            return $returnData;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}


function editRule($POST)
{

    global $company_id;
    global $created_by;



    $returnData = [];

    $isValidate = validate($POST, [
        "dep_key" => "required",
        "asset_class" => "required",
        "wdv" => "required",
        "slm" => "required"

    ]);

    if ($isValidate["status"] == "success") {

        $dep_key = $POST['dep_key'];
        $parent = $POST['parent'] ?? 0;
        $asset_class = $POST['asset_class'];
        $wdv = $POST['wdv'];
        $slm = $POST['slm'];
        $id = $POST['asset_id'];

        $update = queryUpdate("UPDATE `erp_depreciation_table` SET `desp_key`='" . $dep_key . "',`asset_class`='" . $asset_class . "',`parent_code`='" . $parent . "',`wdv`='" . $wdv . "',`slm`='" . $slm . "',`updated_by`='" . $created_by . "' WHERE `depreciation_id` = $id");
        //    console($update);
        //    exit();

        if ($update['status'] == 'success') {
            $returnData['status'] = 'success';
            $returnData['message'] = 'Updated Successfully';
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'Something went wrong';
        }
    } else {
        $returnData['status'] = 'warning';
        $returnData['message'] = 'invalid form input';
    }

    return $returnData;
}
?>

//*************************************/END/******************************************//