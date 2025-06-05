<?php
// Required files
require_once dirname(__DIR__) . "/connection-branch-admin.php";
require_once dirname(__DIR__) . "/functions/branch/func-bom-controller.php";

// statements
function asset_depreciation($company_id = null, $location_id = null, $branch_id = null)
{
    $date = date("Y-m-d");
    //$month_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_start`='$date' AND `company_id`=$company_id");
    $assets = queryGet("SELECT * FROM `erp_asset_use` WHERE `company_id`=$company_id", true);
    $company = queryGet("SELECT * `erp_companies` WHERE `company_id` = $company_id");
    $dep_rule = $company['depreciation_type'];

    foreach ($assets['data'] as $data) {
        $last_val = $data['depreciated_asset_value'] - $data['scrap_value'];
        if ($data['depreciated_asset_value'] != 0 || $last_val != 0) {
            $buying_val = $data['rate'];
            $asset_use_id = $data['use_asset_id'];
            $last_asset_val = $data['depreciated_asset_value'];
            if ($dep_rule == "WDV") {
                $dep_on_val = $last_asset_val;
            } elseif ($dep_rule == "SLM") {
                $dep_on_val = $asset_buying_val;
            }

            $dep_percentage = $data['dep_percentage'];
            $dep_val = ($dep_percentage / 100) * $dep_on_val;
            $asset_new_val =  $last_asset_val  - $depreciation_amount;

            if ($data['depreciation_schedule'] = "first day of month" && $date == date("Y-m-01")) {
                $update = queryUpdate("UPDATE `erp_asset_use` SET `depreciation_amount`= '" . $dep_val . "',`depreciated_asset_value`='" . $asset_new_val . "' WHERE `use_asset_id`= $asset_use_id");
                $insert = queryInsert("INSERT INTO `erp_asset_depreciation` SET `asset_use_id`='" . $asset_use_id . "',`asset_id`=$asset_id,`asset_value`='" . $buying_val . "',`depreciation_on_value`='" . $dep_on_val . "',`depreciated_value`=$dep_val,`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by` = '" . $created_by . "'");
            } else if ($data['depreciation_schedule'] = "15th of month" && $date == date("Y-m-15")) {
                $update = queryUpdate("UPDATE `erp_asset_use` SET `depreciation_amount`= '" . $dep_val . "',`depreciated_asset_value`='" . $asset_new_val . "' WHERE `use_asset_id`= $asset_use_id");
                $insert = queryInsert("INSERT INTO `erp_asset_depreciation` SET `asset_use_id`='" . $asset_use_id . "',`asset_id`=$asset_id,`asset_value`='" . $buying_val . "',`depreciation_on_value`='" . $dep_on_val . "',`depreciated_value`=$dep_val,`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by` = '" . $created_by . "'");
            } else if ($data['depreciation_schedule'] = "last day of month" && $date == date("Y-m-t")) {
                $update = queryUpdate("UPDATE `erp_asset_use` SET `depreciation_amount`= '" . $dep_val . "',`depreciated_asset_value`='" . $asset_new_val . "' WHERE `use_asset_id`= $asset_use_id");
                $insert = queryInsert("INSERT INTO `erp_asset_depreciation` SET `asset_use_id`='" . $asset_use_id . "',`asset_id`=$asset_id,`asset_value`='" . $buying_val . "',`depreciation_on_value`='" . $dep_on_val . "',`depreciated_value`=$dep_val,`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by` = '" . $created_by . "'");
            }
        }
    }
}


$company_sql = queryGet("SELECT * FROM `erp_companies` WHERE 1", true);

foreach ($company_sql['data'] as $data) {
    // console($data);
    asset_depreciation($data['company_id']);
}
