<?php
$current_url = $_SERVER['REQUEST_URI'];

// Check if the URL contains "branch" or "company"
if (strpos($current_url, 'company') !== false) {
    // User is on a company-related page
    require_once("app/v1/connection-company-admin.php");
} elseif (strpos($current_url, 'branch') !== false) {
    // User is on a branch-related page    
    require_once("app/v1/connection-branch-admin.php");
} else {
    // URL doesn't match either branch or company
    require_once("app/v1/connection-branch-admin.php");
}

if ($_REQUEST['act'] == 'imageUpload') {
    // Retrieve the screenshot data sent from the AJAX request
    $fullURL = $_POST['fullURL'];
    $screenshotData = $_POST['screenshot'];

    // Generate a unique filename for the screenshot
    $filename = $_POST['filename'];

    // Specify the target directory where the screenshot will be saved
    $targetDir = BASE_DIR . 'uploads/bugimages/';

    // Save the screenshot to the target directory
    file_put_contents($targetDir . $filename, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $screenshotData)));

    // Save the screenshot information to the database
    // Perform your database operations here, e.g., insert the filename and other relevant details into a table

    // Return a response (optional)
    echo $filename . $targetDir;
} else if ($_REQUEST['act'] == 'bug_submit') {
    $dataArray = $_REQUEST;

    $fullURL = $dataArray['fullURL'];
    $screenshotData = $dataArray['bug_image'];

    $attchment = $_FILES;

    // Generate a unique filename for the screenshot
    $filename = $dataArray['bug_image_url'];

    // Specify the target directory where the screenshot will be saved
    $targetDir = BASE_DIR . 'uploads/bugimages/';

    // Save the screenshot to the target directory
    // file_put_contents($targetDir . $filename, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $screenshotData)));
    // move_uploaded_file($_FILES['attachment']['tmp_name'], $targetDir . $attchment['attachment']['name']);

    // -------------------S3 Bucket File Uplaod Start------------------
    $base64 = $_REQUEST['bug_image'];
    $filename = $_REQUEST['bug_image_url']; 

    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
    $imageData = base64_decode($base64);

    $tmpFile = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($tmpFile, $imageData);

    $fileArray = [
        'name' => $filename,
        'type' => mime_content_type($tmpFile),
        'tmp_name' => $tmpFile,
        'error' => 0,
        'size' => filesize($tmpFile),
    ];

    
    $upload = uploadFileS3($fileArray, $targetDir, ['pdf', 'jpg', 'png', 'jpeg'], 5 * 1024 * 1024);

    $attchmentfile = uploadFileS3($attchment['attachment'], $targetDir, ['pdf', 'jpg', 'png', 'jpeg']);
    
    // ---------------Upload File End----------------

    $bug_code = 'ER77' . date('Ym') . rand(1111, 9999);

      //check count table
      $count_sql = queryGet("SELECT * FROM `erp_bug_user_details` WHERE `user_type` = 'Performer' AND `working_status` = 'Y' AND `fldAdminStatus` = 'active' ORDER BY `open_bug_count` ASC LIMIT 1");
    //   console($count_sql);
      //exit();
      if($count_sql['status'] == 'success'){
   
          $new_count = $count_sql['data']['open_bug_count'] + 1;
           $user = $count_sql['data']['fldAdminKey'];  
        //   $id = $count_sql['data']['count_id'];
     
      }
    

    $ins = "INSERT INTO `erp_bug_list` SET `bug_code`='" . $bug_code . "', `module_name`='" . $dataArray['bug_module_name'] . "',`sub_module_name`='" . $dataArray['bug_sub_module_name'] . "',`page_name`='" . $dataArray['bug_page_name'] . "',`page_url`='" . $dataArray['bug_page_url'] . "',`extra_images`='".$attchment['attachment']['name']."',`assign_to` = '".$user."',`bug_description`='" . $dataArray['bug_description'] . "',`image_url`='" . $dataArray['bug_image_url'] . "',`company_id`='$company_id',`created_user`='$current_userName',`created_by`='$created_by',`status` = 'assigned'";

    $update_count = queryUpdate("UPDATE `erp_bug_user_details` SET  `open_bug_count` = $new_count  WHERE `fldAdminKey` = $user ");


    $responce = queryInsert($ins);
    if ($responce['status'] == 'success') {
        $returnData['status'] = "success";
        $returnData['bug_code'] = $bug_code;
        $returnData['attchment'] = $attchment['attachment']['name'];
        $returnData['message'] = "Thank you for your support.";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
    }
    $returnData['qry'] = $responce;
    echo json_encode($returnData);
}
