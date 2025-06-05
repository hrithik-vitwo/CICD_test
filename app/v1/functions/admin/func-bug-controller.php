<?php
// require_once(BASE_URL . "app/v1/fun-chat-controller.php");
function post_comment($POST){
    global $dbCon;
    global $created_by;
    global $updated_by;
    $returnData = [];
    $bug_id = $POST['bug_id'];
    $convo = $POST['bug_comment'];

    $insert = queryInsert("INSERT INTO `erp_bug_conversation` SET `bug_id`= $bug_id , `conversation`  = '".$convo."',`created_by`= '".$created_by."',`updated_by`='".$updated_by."' ");

    if($insert['status'] == "success"){
        $returnData['status'] = "success";
        $returnData['message'] = "success";

    }
    else{
        $returnData['status'] = "warning";
        $returnData['message'] = "failed";
    }
    
   return $returnData;
}
function assign_username_func($fldAdminKey){
    return $get = queryGet("SELECT * FROM `erp_bug_user_details` WHERE fldAdminKey=$fldAdminKey"); 

}

function assign_func($POST)
{
    // console($POST);
    // exit();

    global $dbCon;
    global $created_by;
    global $updated_by;
    global $company_id;
    global $branch_id;
    global $location_id;

    $returnData = [];
    $bug_id = $POST['bug_id'];
    $assign_to = $POST['assign_to'];
    $reviewer=$POST['reviewer'];
    $assign_date = date("Y-m-d");
    $duration = $POST['durationtime'];

     $sql_list=queryGet("SELECT `fldAdminName`,`fcm_token`,`open_bug_count` FROM `erp_bug_user_details` WHERE fldAdminKey=".$assign_to.";");

    if($sql_list['status']=='success' && !empty($sql_list['data']['fcm_token'])){
    $new_count = $sql_list['data']['open_bug_count'] + 1;
    $adminName=$sql_list['data']['fldAdminName'];
    $fcmToken=$sql_list['data']['fcm_token'];
    $msg="Hi $adminName this bug is assigned for you ";
    $title="Assignment of Bug";
    $tableName='erp_bug_list';
    
    // $saveNotificatons= saveNotification($company_id,$branch_id,$location_id,$tableName,$bug_id,$title,$msg,$created_by,$updated_by)
    // console($saveNotificatons);
    // exit();
    // console($sql_list);
    $notification = sendNotification($fcmToken,$title,$msg);
 
    // console($notification);
    // console("Hiii");
    }

    $insert = queryUpdate("UPDATE `erp_bug_list` SET `assign_to`= '".$assign_to."' , `reviewer`= '".$reviewer."' ,`assign_date`  = '".$assign_date."',`duration`= $duration,`status`= 'assigned',`updated_by`='".$updated_by."' WHERE `id`=$bug_id");

    // console($insert);
    // exit();

    if($insert['status'] == "success"){

        //update_bug_count
        $update_count = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = $new_count WHERE  fldAdminKey=".$assign_to.";");

         if($update_count['status'] == 'success'){
            $returnData['status'] = "success";
            $returnData['message'] = "success";
         }
         else{

            $returnData['status'] = "warning";
            $returnData['message'] = "failed";

         }

           
    }
    else{
        $returnData['status'] = "warning";
        $returnData['message'] = "failed";
    }
    
   return $returnData;

}
function transfar_func($POST)
{
    
    global $dbCon;
    global $created_by;
    global $updated_by;
    global $company_id;
    global $branch_id;
    global $location_id;

    $returnData = [];
    $bug_id = $POST['bug_id'];
    $assign_to = $POST['assign_to'];
    $reviewer=$POST['reviewer'];
    $assign_date = date("Y-m-d");
    $duration = $POST['durationtime'];
    $user_id=$POST['user_id'];

     $sql_list=queryGet("SELECT `fldAdminName`,`fcm_token`,`open_bug_count` FROM `erp_bug_user_details` WHERE fldAdminKey=".$assign_to.";");

    if($sql_list['status']=='success' && !empty($sql_list['data']['fcm_token'])){
    $new_count = $sql_list['data']['open_bug_count'] + 1;
    $adminName=$sql_list['data']['fldAdminName'];
    $fcmToken=$sql_list['data']['fcm_token'];
    $msg="Hi $adminName this bug is assigned for you ";
    $title="Assignment of Bug";
    $tableName='erp_bug_list';
    $notification = sendNotification($fcmToken,$title,$msg);
    }

    $insert = queryUpdate("UPDATE `erp_bug_list` SET `assign_to`= '".$assign_to."' , `reviewer`= '".$reviewer."' ,`assign_date`  = '".$assign_date."',`duration`= $duration,`status`= 'assigned',`updated_by`='".$updated_by."' WHERE `id`=$bug_id");

    $sql_list1=queryGet("SELECT `fldAdminName`,`fcm_token`,`open_bug_count` FROM `erp_bug_user_details` WHERE fldAdminKey=".$user_id."");
    if($sql_list1['status']=='success'){
        $bug_count = $sql_list1['data']['open_bug_count'] -1;
        $update_bug_count = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = $bug_count WHERE  fldAdminKey=".$user_id.";");
    }


    if($insert['status'] == "success"){

        //update_bug_count
        $update_count = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = $new_count WHERE  fldAdminKey=".$assign_to.";");

         if($update_count['status'] == 'success'){
            $returnData['status'] = "success";
            $returnData['message'] = "success";
         }
         else{

            $returnData['status'] = "warning";
            $returnData['message'] = "failed";

         }

           
    }
    else{
        $returnData['status'] = "warning";
        $returnData['message'] = "failed";
    }
    
   return $returnData;

}
?> 