<?php
require_once('../../../app/v1/connection-branch-admin.php');
require_once("../../../app/v1/fun-chat-controller.php");
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $userName = $authCustomer['fldAdminName'];
    $user = $authCustomer['user_type'];
    $company_id = $authCustomer['company_id'];
    // if ($_POST['action'] == "insert") {

    $inputValue = $_POST['input'];
    $bugId = $_POST['bug_id'];
    $createdBy = $_POST['created_by'];
    $updatedBy = $_POST['updated_by'];

    $attachedFile = $_FILES['attachedFile'];
    $targetDir = BUCKET_DIR . "uploads/bugimages/";
    $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
    $maxsize = 2 * 1024 * 1024; // 10 MB
    $fileUploaded = uploadFile($attachedFile, $targetDir, $allowed_types, $maxsize)["data"];

    $sql_list = developerSaveMessage($bugId, $fileUploaded, $user, $userName, $inputValue, $createdBy, $updatedBy);

    $getConversations = getData($bugId, $limit = 10)['data'];

    // $conversations = [];

    // foreach ($getConversations as $key => $value) {
    //     $value['userDetails'] = unserialize($value['userDetails']);
    //     $conversations[] = $value;
        
    //     // remove few fields 
    //     unset(
    //         $conversations[$key]['userDetails']['fcm_token'],
    //         $conversations[$key]['userDetails']['fldAdminStatus'],
    //         $conversations[$key]['userDetails']['fldAdminUpdatedAt'],
    //         $conversations[$key]['userDetails']['fldAdminCreatedAt'],
    //         $conversations[$key]['userDetails']['fldAdminKey'],
    //         $conversations[$key]['userDetails']['fldAdminNotes']
    //     );
    // }

    // Send a JSON response back to the client
    header('Content-Type: application/json');

    if ($sql_list['status'] == "success") {
        sendApiResponse([
            "status" => $sql_list["status"],
            "message" => $sql_list["message"],
            "conversations" => $getConversations
        ], 200);
    } else {
        sendApiResponse([
            "status" => $sql_list["status"],
            "message" => $sql_list["message"],
            "conversations" => []
        ], 404);
    }
    // }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if ($_GET['action'] == 'chats') {
        if (isset($_GET['bug_id']) && $_GET['bug_id'] != "") {
            $bug_id = $_GET['bug_id'];

            // Call function to get data
            $iv_sql = getData($bug_id);

            if ($iv_sql['status'] == "success") {
                $status = $iv_sql["status"];
                $message = $iv_sql["message"];
                $conversations = $iv_sql["data"];

                sendApiResponse([
                    "status" => $status,
                    "message" => $message,
                    "conversations" => $conversations
                ], 200);
            } else {
                sendApiResponse([
                    "status" => $status,
                    "message" => $message,
                    "conversations" => []
                ], 404);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Data not found",
                "conversations" => []
            ], 400);
        }
    }
}
