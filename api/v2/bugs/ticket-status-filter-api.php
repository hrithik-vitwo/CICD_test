<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  
    $authCustomer = authCustomerApiRequest();
    $fldAdminKey = $authCustomer['fldAdminKey'];
    $user_type = $authCustomer['user_type'];

    $pageNo = $_GET['pageNo'] ?? 0;
    $show = $_GET['limit'] ?? 20;
    $start = $pageNo * $show;
    $end = $show;
    $status = $_GET['status'] ?? 'open';
    $cond = "";

    if($_GET['action']=='user_id'){
        if(isset($_GET['user_id'])&& $_GET['user_id']!=""){

        $cond .= "  status NOT IN ('$status') AND `assign_to`='" . $_GET['user_id'] . "' ";
        }

    }else if($_GET['action']=='status'){
        if(isset($_GET['status'])&& $_GET['status']!=""){
        $cond .= " status='" . $_GET['status'] . "' ";
        }
    }else if($_GET['action']=='both'){
        if(isset($_GET['user_id']) && isset($_GET['status']) && $_GET['user_id']!="" && $_GET['status']!=""){
            $cond .= " status='" . $_GET['status'] . "' AND `assign_to`='" . $_GET['user_id'] . "' ";
        }
    }else{
       $cond .="status NOT IN ('$status')";
    }

    
   
    $sql_list ="SELECT * FROM `erp_bug_list` WHERE " . $cond . " ORDER BY erp_bug_list.`created_at` DESC LIMIT " . $start . "," . $end;
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
                } else {
                    sendApiResponse([
                        "status" => "error",
                        "message" => "Method not allowed",
                        "data" => []
                    ], 405);
                }
            


