<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

//echo 'ok';

//console($_FILES);

if ($_FILES["fileToUpload"]["error"] > 0) {
    echo "Error: " . $_FILES["fileToUpload"]["error"];
    exit();
} else {
//    echo "okkk";
    
    $files = $_FILES['fileToUpload'];
  // console($files["name"]);

    $code = $_POST['code'];
    $type = $_POST['type'];
    $reconcilation_type = 'miscellaneous';

   
        //console($key);

        $name = $files["name"];
        $tmpName = $files["tmp_name"];
        $size = $files["size"];

        $allowed_types = ['csv','xlsx'];
        $maxsize = 2 * 1024 * 1024; // 10 MB


        $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
    
        // echo COMP_STORAGE_DIR . "/others/";
        //  console($fileUploaded);
         $image_name = $fileUploaded['data'];
        // exit();



        if ($fileUploaded['status'] == 'success') {
            

            $insimgSql="INSERT INTO `erp_reconciliation` SET 
            `code`=$code,
            `reconciliationType`= '".$reconcilation_type."', 
            `type`='".$type."',
            `files`='" . $image_name . "',
            `company_id`=$company_id,
            `branch_id`=$branch_id,
            `location_id`=$location_id,
            `created_by`='" . $created_by . "',
            `updated_by` ='" . $created_by . "' ";
     $insert_img = queryInsert($insimgSql);
        // echo ($insimgSql);
        }
    }




?>