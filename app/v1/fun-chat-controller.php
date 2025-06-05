<?php
require_once(BASE_DIR . "app/v1/connection-branch-admin.php");
require_once(BASE_DIR . "app/v1/functions/common/func-common.php");


function saveMessage($bug_id, $attachedFile, $conv, $create_by, $update_by)
{
    $targetDir = BASE_DIR . 'uploads/bugimages/';
    $file = $attachedFile;
    $allowedExtensions = ["pdf", "jpeg", "png", "xlsx"];
    $maxSize = 1024 * 1024;
    $minSize = 0;
    $uploadResult = uploadFileS3($file,  $targetDir, $allowedExtensions, $maxSize, $minSize);
    
    $newFilename = basename($uploadResult["data"]['key']);
    $userName=getCreatedByUser($create_by);

    if ($uploadResult['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['message'] = "Thank you for your support.";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        $returnData['sqlResponse'] = $uploadResult;
    }

    if (!empty($conv)) {
        $sql_list = "INSERT INTO `erp_bug_conversation` 
                         SET `bug_id` = '$bug_id',`attatch`='',`user_name`= '$userName',`conversation` = '$conv', `created_by` = '$create_by', `updated_by` = '$update_by'";
    } else {
        $sql_list = "INSERT INTO `erp_bug_conversation` 
                         SET `bug_id` = '$bug_id',`user_name`= '$userName',`attatch` = '$newFilename',`conversation` = '', `created_by` = '$create_by', `updated_by` = '$update_by'";
    }

    $sqlResponse = queryInsert($sql_list);

    if ($sqlResponse['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['message'] = "Thank you for your support.";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        $returnData['sqlResponse'] = $sqlResponse;
    }
    return $returnData;
}

function developerSaveMessage($bug_id, $attachedFile, $user = '', $userName = '', $conv, $create_by, $update_by)
{
    if (!empty($conv)) {
        $sql_list = "INSERT INTO `erp_bug_conversation` 
                         SET `bug_id` = '$bug_id', `user_name`= '$userName', `user_type`='admin', `attatch`='$attachedFile', `conversation` = '$conv'";
    } else {
        $sql_list = "INSERT INTO `erp_bug_conversation` 
                         SET `bug_id` = '$bug_id', `user_name`= '$userName', `user_type`='admin', `attatch` = '$attachedFile',`conversation` = ''";
    }

    $sqlResponse = queryInsert($sql_list);

    if ($sqlResponse['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['message'] = "Thank you for your support.";
    $returnData['sqlResponse'] = $sql_list;
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        $returnData['sqlResponse'] = $sqlResponse;
    }
    return $returnData;
}

function getData($bug_id, $limit=100)
{
    $targetDir = str_replace("\\", "/", dirname(__DIR__, 2) . "/");

    $sql_list = "SELECT 
            `bug_id`,
            `user_type`,
            `conversation`,
            CONCAT('" . $targetDir . "uploads/bugimages/" . "', `attatch`) AS `attatch`,
            `attatch` AS `image_check`,
            TIME_FORMAT(created_at, '%h:%i %p') AS `time`,
            `created_by`,
            `user_name`
            FROM 
            `erp_bug_conversation`
            WHERE 
            `bug_id` = $bug_id  
            ORDER BY `created_at` ASC LIMIT 0,$limit
    ";

    $sqlResponse = queryGet($sql_list, true);

    $datalist = [];
    foreach ($sqlResponse['data'] as $data) {
        $datalist[] = [
            'bug_id'=>$data['bug_id'],
            'user_type'=>$data['user_type'],
            'conversation'=>$data['conversation'],
            'attatch'=>$data['attatch'],
            'image_check'=>getFileUrlS3("upload/bugimages/".$data['image_check']),
            'time'=>$data['time'],
            'created_by'=>$data['created_by'],
            'user_name'=>$data['user_name']

        ];
        
    }
    $returnData['data'] = $datalist;
    if ($sqlResponse['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['message'] = "data found";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        $returnData['sqlResponse'] =  $sql_list;
    }
    return $returnData;
}


function saveNotification($company_id, $branch_id, $location_id, $table_name, $table_id, $title, $description, $created_by, $updated_by)
{

    $sql_list = "INSERT INTO `erp_notification` 
                     SET `company_id` = '$company_id',`branch_id`='$branch_id', `location_id` = '$location_id', `table_name` = '$table_name',`table_id` = '$table_id', `title` = '$title',`description` = '$description',`created_by` = '$created_by',`updated_by` = '$updated_by'";

    $sqlResponse = queryGet($sql_list);

    if ($sqlResponse['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['message'] = "data inserted ";
    } else {
        $returnData['status'] = "Error";
        $returnData['message'] = "Something went wrong!";
        $returnData['sqlResponse'] =  $sql_list;
    }
    return $returnData;
}
