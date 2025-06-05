<?php
require_once("func-notification-controller.php");
$sendNotification = new CustomerNotificationController();

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if ($_GET['action'] == "userlist") {
        $sql_list = "SELECT fldAdminKey,fldAdminName FROM `erp_bug_user_details` WHERE fldAdminStatus='active' AND  user_type='Performer' OR user_type='Approver'";
        $iv_sql = queryGet($sql_list, true);

        if ($iv_sql['status'] == "success") {

            $iv_data = $iv_sql["data"];

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
    } else if ($_GET['action'] == "conversation") {
        if (isset($_GET['bug_id']) && $_GET['bug_id'] != "") {
            $sql_list = "SELECT * FROM `erp_bug_conversation`   WHERE `bug_id` = " . $_GET['bug_id'] . " ORDER BY `created_at` asc";
            $iv_sql = queryGet($sql_list, true);

            if ($iv_sql['status'] == "success") {

                $iv_data = $iv_sql["data"];

                // $data_array = [];
                // foreach ($iv_data as $data) {

                //     $data_array[] = array("items" => $data);
                // }
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
                "status" => "warning",
                "message" => "Bug Id not mentioned",
                "data" => []

            ], 400);
        }
    } else if ($_GET['action'] == "bugstatusupdate") {
        if (isset($_GET['bug_id']) && $_GET['bug_id'] != "" && $_GET['statusSlug'] != '') {
            $authCustomer = authCustomerApiRequest();
            $fldAdminName = $authCustomer['fldAdminName'];

            $sql_list = "UPDATE `erp_bug_list` SET `status`= '" . $_GET['statusSlug'] . "',`updated_by`='" . $fldAdminName . "' WHERE `id`='" . $_GET['bug_id'] . "'";
            $iv_sql = queryUpdate($sql_list);

            if ($iv_sql['status'] == "success") {
                sendApiResponse([
                    "status" => "success",
                    "message" => "Status Updated"

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
                "status" => "warning",
                "message" => "Veriable Missmatch",
                "data" => []

            ], 400);
        }
    } else if ($_GET['action'] == "taskAssign") {
        if (isset($_GET['bug_id']) && $_GET['bug_id'] != "" && $_GET['user_id'] != '' && $_GET['duration'] != '') {
            $authCustomer = authCustomerApiRequest();
            $fldAdminName = $authCustomer['fldAdminName'];
            $assign_date = date("Y-m-d");

            $sql_list = "UPDATE `erp_bug_list` SET `assign_to`= '" . $_GET['user_id'] . "' , `assign_date`  = '" . $assign_date . "',`duration`= '" . $_GET['duration'] . "',`status`= 'assigned',`updated_by`='" . $fldAdminName . "' WHERE `id`='" . $_GET['bug_id'] . "'";



            $iv_sql = queryUpdate($sql_list);

            if ($iv_sql['status'] == "success") {
                sendApiResponse([
                    "status" => "success",
                    "message" => "Task Assigned successfully"

                ], 200);
            } else {
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Not Assigned",
                    "sql_list" => $sql_list,
                    "data" => []

                ], 400);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Veriable Missmatch",
                "data" => []

            ], 400);
        }
    } else if ($_GET['action'] == "barGraph") {
        $authCustomer = authCustomerApiRequest();
        $fldAdminName = $authCustomer['fldAdminName'];

        $fldAdminKey = $authCustomer['fldAdminKey'];
        $user_type = $authCustomer['user_type'];

        $assigned_sql = queryGet("SELECT u.fldAdminKey, u.fldAdminName,
                                            SUM(CASE WHEN b.status IN ('todo', 'assign') THEN 1 ELSE 0 END) AS total_todo_assign,
                                            (SUM(CASE WHEN b.status IN ('todo', 'assign') THEN 1 ELSE 0 END) / 
                                            NULLIF(SUM(CASE WHEN b.status IN ('todo', 'assign', 'solved') THEN 1 ELSE 0 END), 0) * 100) AS todo_assign_percentage,
                                            SUM(CASE WHEN b.status = 'solved' THEN 1 ELSE 0 END) AS total_solved,
                                            (SUM(CASE WHEN b.status = 'solved' THEN 1 ELSE 0 END) / 
                                            NULLIF(SUM(CASE WHEN b.status IN ('todo', 'assign', 'solved') THEN 1 ELSE 0 END), 0) * 100) AS solved_percentage
                                    FROM erp_bug_user_details u
                                    LEFT JOIN erp_bug_list b ON u.fldAdminKey = b.assign_to
                                    GROUP BY u.fldAdminKey, u.fldAdminName
                                    HAVING total_todo_assign > 0 OR total_solved > 0 ORDER BY total_todo_assign DESC", true);
        
        if ($assigned_sql['status'] == "success") {
            sendApiResponse([
                "status" => "success",
                "message" => "Data Fetch successfully",
                "data" => $assigned_sql['data']
                

            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Not found",
                "sql_list" => $sql_list,
                "data" => []

            ], 400);
        }



    } else if ($_GET['action'] == "pieChart") {
        $authCustomer = authCustomerApiRequest();
        $fldAdminName = $authCustomer['fldAdminName'];

        $fldAdminKey = $authCustomer['fldAdminKey'];
        $user_type = $authCustomer['user_type'];

        //'-----------------------
        if ($user_type == "Performer") {

            $assigned_sql = queryGet("SELECT COUNT(`id`) as total_assigned FROM `erp_bug_list` WHERE `status`='assigned' AND assign_to =$fldAdminKey");
            $todo_sql = queryGet("SELECT COUNT(`id`) as total_todo FROM `erp_bug_list` WHERE `status`='todo' AND assign_to =$fldAdminKey");
            $solved_sql = queryGet("SELECT COUNT(`id`) as total_solved FROM `erp_bug_list` WHERE `status`='solved' AND assign_to =$fldAdminKey");

            $assigned = $assigned_sql['data']['total_assigned'];
            $solved = $solved_sql['data']['total_solved'];
            $todo = $todo_sql['data']['total_todo'];

            $queryset = [];
            $totalSum = $assigned + $solved + $todo;

            $queryset['persentage'] = array(
                'assigned' => $assigned / $totalSum * 100,
                'todo' => $todo / $totalSum * 100,
                'solved' => $solved / $totalSum * 100
            );
            $queryset['value'] = array(
                'assigned' => $assigned,
                'todo' => $todo,
                'solved' => $solved
            );
            // console($queryset);
        } else {
            $open_sql = queryGet("SELECT COUNT(`id`) as  total_open FROM `erp_bug_list` WHERE `status`='open'");
            $assigned_sql = queryGet("SELECT COUNT(`id`) as total_assigned FROM `erp_bug_list` WHERE `status`='assigned'");
            $todo_sql = queryGet("SELECT COUNT(`id`) as total_todo FROM `erp_bug_list` WHERE `status`='todo'");
            $solved_sql = queryGet("SELECT COUNT(`id`) as total_solved FROM `erp_bug_list` WHERE `status`='solved'");

            $assigned = $assigned_sql['data']['total_assigned'];
            $open = $open_sql['data']['total_open'];
            $solved = $solved_sql['data']['total_solved'];
            $todo = $todo_sql['data']['total_todo'];

            $queryset = [];
            $totalSum = $assigned + $open + $solved + $todo;

            $queryset['persentage'] = array(
                'open' => $open / $totalSum * 100,
                'assigned' => $assigned / $totalSum * 100,
                'todo' => $todo / $totalSum * 100,
                'solved' => $solved / $totalSum * 100
            );

            $queryset['value'] = array(
                'open' => $open,
                'assigned' => $assigned,
                'todo' => $todo,
                'solved' => $solved
            );
            // console($queryset);

        }


        ///--------------------------

        if (!empty($queryset)) {
            sendApiResponse([
                "status" => "success",
                "message" => "Data Fetch successfully",
                "data" => $queryset

            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Not Found",
                "sql_list" => $sql_list,
                "data" => []

            ], 400);
        }
    } else if ($_GET['action'] == "SendN") {
        $authCustomer = authCustomerApiRequest();
        $fldAdminName = $authCustomer['fldAdminName'];
        $fcm_token = $authCustomer['fcm_token'];
        $assign_date = date("Y-m-d");
        $notification = $sendNotification->sendNotification($fcm_token, 'Hello Joy This Is Your Bug List', 'http://one.vitwo.ai/branch/login.php');

        if ($notification['status'] == "1") {
            sendApiResponse([
                "status" => "success",
                "message" => "Notification successfully Send",
                "sql_list" => $notification,
                "authCustomer" => $authCustomer

            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Error To send",
                "sql_list" => $notification,
                "authCustomer" => $authCustomer

            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Something Went wrong!",
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
