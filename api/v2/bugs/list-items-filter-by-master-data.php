<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
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

    // if ($status == 'open') {
    //     $cond .= " AND erp_bug_list.`status`='" . $status . "'";
    // } else {
    //     if ($user_type == 'Performer') {
    //         $cond .= " AND erp_bug_list.`status`='" . $status . "'";
    //         $cond .= " AND erp_bug_list.`assign_to` = '$fldAdminKey'";
    //     }else{            
    //         $cond .= " AND erp_bug_list.`status`='" . $status . "'";
    //     }
    // }
 
    if ($user_type == 'Performer') {
        if(empty($status)){
            $cond .= " AND erp_bug_list.`status`!='open'";
        }else if($status == 'open') {
            sendApiResponse([
                "status" => "warning",
                "message" => "No Allowed",
                "data" => []

            ], 400);
        }else if(!empty($status)){
            $cond .= " AND erp_bug_list.`status`='" . $status . "'";
        }
    } else {
        if (!empty($status)) {
        $cond .= " AND erp_bug_list.`status`='" . $status . "'";
        }else{
            $cond .= " AND erp_bug_list.`status`!='open'";
        }
    }
    if(!empty($user_id)){
        $cond .= " AND erp_bug_list.`assign_to` = '$user_id'";
    }




    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND erp_bug_list.`created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (erp_bug_list.`bug_description` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`bug_code` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`page_url` like '%" . $_POST['keyword'] . "%' OR erp_bug_user_details.`fldAdminName` like '%" . $_POST['keyword'] . "%')";
    }

    $open_count=queryGet("SELECT COUNT(*) as open_count FROM `erp_bug_list` WHERE status='open';")['data']['open_count']??0;
    $todo_count=queryGet("SELECT COUNT(*) as todo_count FROM `erp_bug_list` WHERE 1 " . $cond . " status='todo'")['data']['todo_count']??0;
    $solved_count=queryGet("SELECT COUNT(*) as solved_count FROM `erp_bug_list` WHERE 1 " . $cond . " status='solved'")['data']['solved_count']??0;

    $bugImg = BASE_URL . 'uploads/bugimages/';
    $sql_list = "SELECT
                erp_bug_list.id,
                erp_bug_list.bug_code,
                erp_bug_list.module_name,
                erp_bug_list.page_url,
                erp_bug_list.created_user,
                erp_bug_list.duration,
                erp_bug_list.page_name,
                erp_bug_list.bug_description,
                erp_bug_list.assign_to,
                IF(erp_bug_list.image_url IS NOT NULL, CONCAT('" . $bugImg . "', erp_bug_list.image_url), NULL) AS image_url,
                erp_bug_list.assign_date,
                erp_bug_list.duration,
                erp_bug_list.status,
                erp_bug_user_details.fldAdminName,
                erp_bug_user_details.flAdminDesignation,
                COALESCE(erp_companies.company_name, '-') AS company_name
            FROM
                erp_bug_list
            LEFT JOIN
                erp_bug_user_details
            ON
                erp_bug_list.assign_to = erp_bug_user_details.fldAdminKey
            LEFT JOIN
                erp_companies
            ON
                erp_bug_list.company_id = erp_companies.company_id
            WHERE 1 " . $cond . "           
            ORDER BY erp_bug_list.`created_at` ASC LIMIT " . $start . "," . $end;


    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        // $data_array = [];
        // foreach ($iv_data as $data) {

        //     $data_array[] = array("items" => $data);
        // }
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "data found",
            "todo_count" => $todo_count,
            "solved_count" => $solved_count,
            "open_count" => $open_count,
            "data" => $iv_data

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Not found",
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
//echo "ok";