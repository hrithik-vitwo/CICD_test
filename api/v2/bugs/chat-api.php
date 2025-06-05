<?php
require_once('../../../app/v1/connection-branch-admin.php');
require_once("../../../app/v1/fun-chat-controller.php");
require_once("api-common-func.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['action'] == "insert") {

         $inputValue = $_POST['input']; 
        $attachedFile = $_FILES['attachedFile'];
        $targetDir = BASE_DIR.'uploads/bugimages/';
        // Additional data
        $bugId = $_POST['bug_id'];
        $createdBy = $_POST['created_by'];
        $updatedBy = $_POST['updated_by'];
       
        $sql_list = saveMessage($bugId,$attachedFile,$inputValue, $createdBy,$updatedBy);
        
        header('Content-Type: application/json');
        echo json_encode($sql_list);
    }
}else if($_SERVER["REQUEST_METHOD"] == "GET"){

    if ( $_GET['action'] == 'chats' ) {
        if(isset($_GET['bug_id'])&& $_GET['bug_id'] != ""){
        $bug_id = $_GET['bug_id'];

        // Call function to get data
        $iv_sql = getData($bug_id);

        if ($iv_sql['status'] == "success") {
            $iv_data = $iv_sql["data"];
            sendApiResponse([
                "status" => "success",
                "message" => "Data found",
                "data" => $iv_data
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Data not found",
                "data" => $iv_sql
            ], 404);
        }
     }else{
        sendApiResponse([
            "status" => "warning",
            "message" => "Data not found",
            "data" => $iv_sql
        ], 400);
     }
    }
}