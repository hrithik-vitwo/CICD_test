<?php
require_once("../../../app/v1/connection-company-admin.php");
$headerData = array('Content-Type: application/json');
$responce = [];
$user_id = $_SESSION["logedCompanyAdminInfo"]["adminId"];
$page_name = $_GET['page'];
$_GET['dashboard_id']=1;
$masterSql = "SELECT * FROM `" . ERP_DASH_COMPONENT_MASTER . "` WHERE `page_name` = '" . $page_name . "' AND status='active'";
$masterSqlresult = queryGet($masterSql);
if ($masterSqlresult['status'] = 'success') {
    $page_id = $masterSqlresult['data']['component_id'];

    if (isset($_GET['dashboard_id']) && !empty($_GET['dashboard_id'])) {
        $dashboard_id = $_GET['dashboard_id'];

        $sql = "SELECT
            dashComponent.*,
            userDash.`company_id`,
            userDash.`user_id`,
            dashCompMaster.`page_name`,
            dashCompMaster.`component_title`,
            dashCompMaster.`divArea`,
            dashCompMaster.`component_desc`
        FROM
            `" . ERP_DASH_COMPONENT . "` AS dashComponent,
            `" . ERP_USER_DASHBOARD . "` AS userDash,
            `" . ERP_DASH_COMPONENT_MASTER . "` AS dashCompMaster
        WHERE
            dashComponent.dashboard_id = userDash.dashboard_id 
            AND dashComponent.component_id = dashCompMaster.component_id 
            AND dashCompMaster.`page_name` = '" . $page_name . "' 
            AND userDash.`company_id` = " . $company_id . " 
            AND userDash.`user_id` = " . $user_id . "";

        $res = queryGet($sql);
        if ($res['numRows'] == 0) {
            $insComp = "INSERT INTO `" . ERP_DASH_COMPONENT . "` SET `dashboard_id`='" . $dashboard_id . "',`component_id`='" . $page_id . "',`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "'";
            
            $resDash = queryInsert($insComp);
            $responce = $resDash;
            $responce['errNo'] = '1st';
        } else {
            $responce = [
                "status" => "warning",
                "message" => "This already added in your Dashboard!",
                "errNo" => "1st else"
            ];
        }
    } else {
        $dasSql = "SELECT * FROM `" . ERP_USER_DASHBOARD . "` WHERE company_id = $company_id AND `user_id`=" . $user_id . " AND status='active'";
        $dasSqlresult = queryGet($dasSql);
        if ($dasSqlresult['numRows'] == 0) {
            $insDas = "INSERT INTO `" . ERP_USER_DASHBOARD . "` 
                    SET
                        `company_id`='" . $company_id . "',
                        `user_id`='" . $user_id . "',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "'";
            $resDash = queryInsert($insDas);
            if ($resDash['status'] == 'success') {
                $dashboard_id = $resDash['insertedId'];

                $insComp = "INSERT INTO `" . ERP_DASH_COMPONENT . "` SET `dashboard_id`='" . $dashboard_id . "',`component_id`='" . $page_id . "',`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "'";

                $resComp = queryInsert($insComp);


                $responce = $resComp;
                $responce['errNo'] = '2nd';
            } else {
                $responce = $resDash;
                $responce['message'] = 'Dashboard creation Faild!';
                $responce['errNo'] = '2nd else';
            }
        } else {
            $dashboard_id = $dasSqlresult['data']['dashboard_id'];

            $sql = "SELECT
                        dashComponent.*,
                        userDash.`company_id`,
                        userDash.`user_id`,
                        dashCompMaster.`page_name`,
                        dashCompMaster.`component_title`,
                        dashCompMaster.`divArea`,
                        dashCompMaster.`component_desc`
                    FROM
                        `" . ERP_DASH_COMPONENT . "` AS dashComponent,
                        `" . ERP_USER_DASHBOARD . "` AS userDash,
                        `" . ERP_DASH_COMPONENT_MASTER . "` AS dashCompMaster
                    WHERE
                        dashComponent.dashboard_id = userDash.dashboard_id 
                        AND dashComponent.component_id = dashCompMaster.component_id 
                        AND dashCompMaster.`page_name` = '" . $page_name . "' 
                        AND userDash.`company_id` = " . $company_id . " 
                        AND userDash.`user_id` = " . $user_id . "";

            $res = queryGet($sql);
            if ($res['numRows'] == 0) {
                $insComp = "INSERT INTO `" . ERP_DASH_COMPONENT . "` SET `dashboard_id`='" . $dashboard_id . "',`component_id`='" . $page_id . "',`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "'";
                $resComp = queryInsert($insComp);
                $responce = $resComp;
                $responce['errNo'] = '3rd';
                $responce['dasSqlresult'] = $dasSqlresult;
            } else {
                $responce = [
                    "status" => "warning",
                    "message" => "This already added in your Dashboard!",
                    "errNo" => "3rd else"
                ];
            }
        }
    }
} else {
    $responce = [
        "status" => "warning",
        "message" => "This Graph Component not found!"
    ];
}


echo json_encode($responce, true);
