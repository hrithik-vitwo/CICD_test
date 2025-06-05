<?php
// Required files
require_once dirname(__DIR__) . "/connection-branch-admin.php";
require_once dirname(__DIR__) . "/functions/branch/func-bom-controller.php";

// statements
function day_var_check($company_id = null, $location_id = null, $branch_id = null)
{
    $date = date('Y-m-d', strtotime("-1 days"));
    // $today = date("Y-m");
    $month_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE MONTH(month_start) = MONTH(CURRENT_DATE()) AND YEAR(month_start) = YEAR(CURRENT_DATE())  AND `company_id`=$company_id");
    if ($month_sql['status'] == "success") {
        $month_data = $month_sql['data'];
        $var_id = $month_data['month_variant_id'];
        $last_date = $month_data['month_end'];
        $time = "23:59:59";
        $last_date_time = $last_date . " " . $time;

        $day_sql = queryGet("SELECT * FROM `tbl_branch_admin_details`WHERE DATE(flAdminVariantLastDate)=DATE_FORMAT('$date','%Y-%m-%d') AND `fldAdminCompanyId`=$company_id", true);
        if ($day_sql['status'] == "success") {
            $day_data = $day_sql['data'];
            // console($day_data);
            foreach ($day_data as $data) {
                // console($last_date_time);
                $fldAdminKey = $data['fldAdminKey'];
                $update = "UPDATE `tbl_branch_admin_details` SET `flAdminVariant`=$var_id, `flAdminVariantLastDate`='$last_date_time' WHERE `fldAdminKey`=$fldAdminKey";
                $updateObj = queryUpdate($update);
                echo $updateObj["status"];
                $insert_sql = queryInsert("INSERT `erp_admin_variant_log` SET `admin_id`= $fldAdminKey , `variant_id` = $var_id");
                echo  "\n".$update["status"];
            }
        } else {
            echo "error";
        }
    } else {
        echo "error2";
    }
}

$company_sql = queryGet("SELECT * FROM `erp_companies` WHERE 1", true);

foreach ($company_sql['data'] as $data) {
    day_var_check($data['company_id']);
}
