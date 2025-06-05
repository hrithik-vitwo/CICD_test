<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();

    $fldAdminKey = $authCustomer['fldAdminKey'];
    $user_type = $authCustomer['user_type'];
    $user_id = $_POST['user_id'];

    $pageNo = $_POST['pageNo'] ?? 0;
    $show = $_POST['limit'] ?? 20;
    $start = $pageNo * $show;
    $end = $show;
    $status = $_POST['status'] ?? '';
    $cond = "";

    if ($user_type == 'Performer') {
        if (empty($status)) {
            $cond .= " AND erp_bug_list.`status`!='open'";
        } else if ($status == 'open') {
            sendApiResponse([
                "status" => "warning",
                "message" => "No Allowed",
                "data" => []

            ], 400);
        } else if (!empty($status)) {
            $cond .= " AND erp_bug_list.`status`='" . $status . "'";
        }
    } else {
        if (!empty($status)) {
            $cond .= " AND erp_bug_list.`status`='" . $status . "'";
        } else {
            $cond .= " AND erp_bug_list.`status`!='open'";
        }
    }
    if (!empty($user_id)) {
        $cond .= " AND erp_bug_list.`assign_to` = '$user_id'";
    }

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND erp_bug_list.`created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (erp_bug_list.`bug_description` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`bug_code` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`page_url` like '%" . $_POST['keyword'] . "%' OR erp_bug_user_details.`fldAdminName` like '%" . $_POST['keyword'] . "%')";
    }

    $open_count = queryGet("SELECT COUNT(*) as open_count FROM `erp_bug_list` WHERE status='open';")['data']['open_count'] ?? 0;
    $todo_count = queryGet("SELECT COUNT(*) as todo_count FROM `erp_bug_list` WHERE 1 " . $cond . " status='todo'")['data']['todo_count'] ?? 0;
    $solved_count = queryGet("SELECT COUNT(*) as solved_count FROM `erp_bug_list` WHERE 1 " . $cond . " status='solved'")['data']['solved_count'] ?? 0;

    $bugImg = BASE_URL . 'uploads/bugimages/';

    $sql_list = "SELECT 
            u.fldAdminKey AS fldAdminKey, 
            u.flAdminDesignation AS flAdminDesignation,
            u.fldAdminUserName AS fldAdminUserName,
            COUNT(CASE WHEN b.completed_date IS NOT NULL THEN 1 END) AS closed_tasks_count,
            `b.completed_date` AS open_tasks_count
        FROM 
            erp_bug_user_details u
        LEFT JOIN 
            erp_bug_list b ON u.fldAdminKey = b.assign_to
            LEFT JOIN
                        erp_companies AS comp
                    ON
                        b.company_id = comp.company_id
            
        GROUP BY 
            u.fldAdminKey, u.fldAdminUserName
    ";

    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        sendApiResponse([
            "status" => "success",
            "message" => "data found",
            "data" => $iv_data,
            "iv_sql" => $iv_sql

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Not found",
            "sql_list" => $sql_list,
            "data" => []

        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
