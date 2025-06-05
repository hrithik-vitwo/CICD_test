<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $fldAdminKey = $authCustomer['fldAdminKey'];
    $user_type = $authCustomer['user_type'];

    $pageNo = $_POST['pageNo'] ?? 0;
    $show = $_POST['limit'] ?? 20;
    $start = $pageNo * $show;
    $end = $show;
    // $status = $_POST['status'] ?? 'todo';
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
        if ($status == 'open') {
            sendApiResponse([
                "status" => "warning",
                "message" => "No Allowed",
                "data" => []

            ], 400);
        } else {
            $cond .= " AND erp_bug_list.`assign_to` = '$fldAdminKey'";
            $cond .= " AND erp_bug_list.`status`='" . $status . "'";
        }
    } else {
        $cond .= " AND erp_bug_list.`status`='" . $status . "'";
    }




    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND erp_bug_list.`created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (erp_bug_list.`bug_description` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`bug_code` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`page_url` like '%" . $_POST['keyword'] . "%' OR erp_bug_user_details.`fldAdminName` like '%" . $_POST['keyword'] . "%')";
    }
    $bugImg = BASE_URL . 'uploads/bugimages/';
    $sql_list = "SELECT
               
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
            "data" => $iv_data

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Not found",
            "sql_list" => $sql_list,
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";