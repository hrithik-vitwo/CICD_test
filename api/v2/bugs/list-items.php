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
    $status = $_POST['status'] ?? 'todo';
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
            $cond .= " AND erp_bug_list.`status`='".$status."'";
        }
    } else {
       $cond .= " AND erp_bug_list.`status`='".$status."'";
    }




    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND erp_bug_list.`created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (erp_bug_list.`bug_description` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`bug_code` like '%" . $_POST['keyword'] . "%' OR erp_bug_list.`page_url` like '%" . $_POST['keyword'] . "%' OR erp_bug_user_details.`fldAdminName` like '%" . $_POST['keyword'] . "%')";
    }
    // $bugImg = BASE_URL . 'uploads/bugimages/';
    // $sql_list = "SELECT
    //                 erp_bug_list.id,
    //                 erp_bug_list.bug_code,
    //                 erp_bug_list.module_name,
    //                 erp_bug_list.sub_module_name,
    //                 erp_bug_list.page_name,
    //                 erp_bug_list.page_url,
    //                 erp_bug_list.bug_description,
    //                 IF(erp_bug_list.image_url IS NOT NULL, CONCAT('".$bugImg."',erp_bug_list.image_url), NULL) AS image_url,
    //                 erp_bug_list.extra_images,
    //                 erp_bug_list.assign_to,
    //                 erp_bug_list.assign_date,
    //                 erp_bug_list.duration,
    //                 erp_bug_list.start_date,
    //                 erp_bug_list.completed_date,
    //                 erp_bug_list.created_at,
    //                 erp_bug_list.created_user,
    //                 erp_bug_list.updated_at,
    //                 erp_bug_list.updated_by,
    //                 erp_bug_list.status,
    //                 erp_bug_user_details.fldAdminName,
    //                 erp_bug_user_details.flAdminDesignation
    //             FROM
    //                 erp_bug_list
    //             LEFT JOIN
    //                 erp_bug_user_details
    //             ON
    //                 erp_bug_list.assign_to = erp_bug_user_details.fldAdminKey

    //         WHERE 1 " . $cond . " ORDER BY erp_bug_list.`created_at` ASC limit " . $start . "," . $end . " ";

    $bugImg = BASE_URL . 'uploads/bugimages/';
    $sql_list = "SELECT
                erp_bug_list.id,
                erp_bug_list.bug_code,
                erp_bug_list.module_name,
                erp_bug_list.sub_module_name,
                erp_bug_list.page_name,
                erp_bug_list.page_url,
                erp_bug_list.bug_description,
                erp_bug_list.image_url,
                erp_bug_list.extra_images,
                erp_bug_list.assign_to,
                erp_bug_list.assign_date,
                erp_bug_list.duration,
                erp_bug_list.start_date,
                erp_bug_list.completed_date,
                erp_bug_list.created_at,
                erp_bug_list.created_user,
                erp_bug_list.updated_at,
                erp_bug_list.updated_by,
                erp_bug_list.status,
                erp_bug_list.bug_note,
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
            ORDER BY erp_bug_list.`created_at` DESC LIMIT " . $start . "," . $end ;


    $iv_sql = queryGet($sql_list, true);

     $data_array = [];
    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

       
        foreach ($iv_data as $data) {

            $data_array[] = [
                'id'=>$data['id'],
                'bug_code'=>$data['bug_code'],
                'module_name'=>$data['module_name'],
                'sub_module_name'=>$data['sub_module_name'],
                'page_name'=>$data['page_name'],
                'page_url'=>$data['page_url'],
                'bug_description'=>$data['bug_description'],
                'image_url'=>getFileUrlS3("upload/bugimages/".$data['image_url']),
                'extra_images'=>getFileUrlS3("upload/bugimages/".$data['extra_images']),
                'assign_to'=>$data['assign_to'],
                'assign_date'=>$data['assign_date'],
                'duration'=>$data['duration'],
                'start_date'=>$data['start_date'],
                'completed_date'=>$data['completed_date'],
                'created_at'=>$data['created_at'],
                'created_user'=>$data['created_user'],
                'updated_at'=>$data['updated_at'],
                'updated_by'=>$data['updated_by'],
                'status'=>$data['status'],
                'bug_note'=>$data['bug_note'],
                'fldAdminName'=>$data['fldAdminName'],
                'flAdminDesignation'=>$data['flAdminDesignation'],
                'company_name'=>$data['company_name']  
            ];
        }
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "data found",
            "data" => $data_array

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