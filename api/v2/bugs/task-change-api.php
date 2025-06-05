<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $fldAdminKey = $authCustomer['fldAdminKey'];
    $user_type = $authCustomer['user_type'];
    $admin_id = $authCustomer['fldAdminKey'];
      // $pageNo = $_POST['pageNo'] ?? 0;
    // $show = $_POST['limit'] ?? 20;
    // $start = $pageNo * $show;
    // $end = $show;
   $user_id = $_POST['user_id'];
   $task_id = $_POST['task_id'];
   $note = $_POST['note'];

   $change_user = queryUpdate("UPDATE `erp_bug_list` SET `assign_to` = $user_id , `bug_note` = '".$note."' WHERE `id` = $task_id");

   if($change_user['status'] == 'success'){
   
    $check_count = queryGet("SELECT * FROM `erp_bug_user_details` WHERE `fldAdminKey` = $user_id");

    if($check_count['status'] == 'success'){
       // $count_id = $check_count['data']['count_id'];
        $new_count = $check_count['data']['open_bug_count'] + 1;
        $update_count = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = $new_count WHERE `fldAdminKey` = $user_id");
       
         if($update_count['status'] == 'success'){

           //  update own task count
        $check_own_count = queryGet("SELECT * FROM `erp_bug_user_details` WHERE `fldAdminKey` = $admin_id");
         $own_task_count = $check_own_count['data']['open_bug_count'] - 1;
       // $own_count_id = $check_own_count['data']['count_id'];
        $update_own_count = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` =  $own_task_count WHERE `fldAdminKey` = $admin_id");

        if($update_own_count['status'] == 'success'){
                    sendApiResponse([
                        "status" => "success",
                        "message" => "Success !"
            
                    ], 200);
                    } 
                    else{
                        sendApiResponse([
                            "status" => "warning",
                            "message" => "Your Task Count Change Failed !"
                
                        ], 415);  
                    }

         }
         else{
                    sendApiResponse([
                        "status" => "warning",
                        "message" => "Assignee's Task Count Change Failed !"
            
                    ], 410);
            
                }


    }
    

}
else{
        sendApiResponse([
            "status" => "warning",
            "message" => "Assignee change failed"
    
        ], 400);
    }
}
else {
        sendApiResponse([
            "status" => "error",
            "message" => "Method not allowed"
        ], 405);
    }


    ?>
<!--    
//     if($update_count['status'] == 'success'){
//         //update own task count
//         $check_own_count = queryGet("SELECT * FROM `erp_user_bug_count` WHERE `user_id` = $admin_id");
//         $own_task_count = $check_own_count['data']['open_bug_count'] - 1;
//         $own_count_id = $check_own_count['data']['count_id'];
//         $update_own_count = queryUpdate("UPDATE `erp_user_bug_count` SET `open_bug_count` =  $own_task_count WHERE `count_id` = $own_count_id");

//         if($update_own_count['status'] == 'success'){
//         sendApiResponse([
//             "status" => "success",
//             "message" => "Success !"

//         ], 200);
//         } 
//         else{
//             sendApiResponse([
//                 "status" => "warning",
//                 "message" => "Your Task Count Change Failed !"
    
//             ], 415);  
//         }

//     }
//     else{
//         sendApiResponse([
//             "status" => "warning",
//             "message" => "Assignee's Task Count Change Failed !"

//         ], 410);

//     }
//    }
// else{
//     sendApiResponse([
//         "status" => "warning",
//         "message" => "Assignee change failed"

//     ], 400);
// }
 
// }
// else {
//     sendApiResponse([
//         "status" => "error",
//         "message" => "Method not allowed"
//     ], 405);
// }

// ?> -->