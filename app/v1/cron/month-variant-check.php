<?php
// Required files
require_once dirname(__DIR__) . "/connection-branch-admin.php";
require_once dirname(__DIR__) . "/functions/branch/func-bom-controller.php";

// statements



//queryInsert("INSERT INTO `test` SET `name`='Cron',`age`=".rand(10,50));

function month_var_check($company_id = null, $location_id = null, $branch_id = null)
{
    $date = date("Y-m-d");
    $month_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_start`='$date' AND `company_id`=$company_id");
    // echo $month_sql;
    if ($month_sql['status'] == "success") {
        $month_data = $month_sql['data'];
        $var_id = $month_data['month_variant_id'];
        $last_date =  $month_data['month_end'];
        $time = "23:59:59";
        $last_date_time = $last_date . " " . $time;

        $users = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminCompanyId`=$company_id", true);
        if ($users['status'] == "success") {
            $user_data = $users['data'];
            foreach ($user_data as $user) {
                $userid = $user['fldAdminKey'];
                $update_sql = "UPDATE `tbl_branch_admin_details` SET `flAdminVariant`= $var_id , `flAdminVariantLastDate` = '$last_date_time' WHERE `fldAdminKey`= $userid";
                $update = queryUpdate($update_sql);
                $insert_sql = queryInsert("INSERT `erp_admin_variant_log` SET `admin_id`= $userid , `variant_id` = $var_id");
                echo "\n" . $update["status"];
            }
        } else {
            echo  "\n" . "error1";
        }
    } else {
        echo  "\n" . "error2";
    }
}


$company_sql = queryGet("SELECT * FROM `erp_companies` WHERE 1", true);
foreach ($company_sql['data'] as $data) {
    // console($data);
    month_var_check($data['company_id']);
}


//}
