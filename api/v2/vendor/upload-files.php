<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    // $vendor_id = 98;
    global $company_id;
    global $branch_id;
    global $location_id;
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $cond = "";

    if (isset($_POST['formDate']) && $_POST['formDate'] != '' && isset($_POST['toDate']) && $_POST['toDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
        $from_date = $_POST['formDate'];
        $to_date = $_POST['toDate'];
    }

    $files = $_FILES['fileToUpload'];
    $type = 'vendor';
    $reconcilation_type = $_POST['reconcilation_type'];

    $name = $files["name"];
    $tmpName = $files["tmp_name"];
    $size = $files["size"];

    $allowed_types = ['csv', 'xlsx'];
    $maxsize = 2 * 1024 * 1024; // 10 MB
    $path1 = BUCKET_DIR."uploads/$company_id/others/";
    $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], $path1, $allowed_types, $maxsize, 0);

    $image_name = $fileUploaded['data'];

    if ($fileUploaded['status'] == 'success') {

        $insimgSql = "INSERT INTO `erp_reconciliation` SET 
            `code`='" . $vendor_id . "',
            `reconciliationType`= '" . $reconcilation_type . "', 
            `type`='" . $type . "',
            `files`='" . $image_name . "',
            `company_id`=$company_id,
            `branch_id`=$branch_id,
            `location_id`=$location_id";
        $insert_img = queryInsert($insimgSql);

        // $fetchFileList = queryGet("SELECT *, CONCAT('" . BUCKET_URL . "uploads/$company_id/others/', `files`) AS `files` FROM `erp_reconciliation` WHERE `reconciliationType` = '" . $reconcilation_type . "' AND `company_id` = '" . $company_id . "' AND `branch_id` = '" . $branch_id . "' AND `location_id` = '" . $location_id . "' AND `code` = '" . $vendor_id . "' AND `type` = '" . $type . "' ORDER BY `id` DESC ", true);

        sendApiResponse([
            "status" => "success",
            "message" => "File uploaded successfully",
            // "data" => $fetchFileList['data']
            "data" => ""
        ]);
    } else {
        sendApiResponse([
            "status" => "error",
            "path" => $path1,
            "message" => $fileUploaded['message'],
            "data" => ""
        ]);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => ""
    ], 405);
}
