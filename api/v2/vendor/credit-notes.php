<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo 1;
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';


    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`credit_note_no` like '%" . $_POST['keyword'] . "%' OR `documentNo` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_credit_notes`   WHERE `company_id`=" . $company_id . " AND `branch_id` = " . $branch_id . " AND `location_id` = " . $location_id . " AND `creditors_type` = 'vendor' AND `party_id`=" . $vendor_id . " " . $cond . " ORDER BY `id` desc limit " . $start . "," . $end . " ";
    $iv_sql = queryGet($sql_list, true);

    
    if ($iv_sql['status'] == "success") {
        
        $iv_data = $iv_sql["data"];
        
        $data_array = [];
        foreach ($iv_data as $data) {
            $companyID = $data['company_id'];
        
            $companyList = "SELECT * FROM `erp_companies`  WHERE `company_id`=" . $companyID . " ";
            $companyDetails = queryGet($companyList);
            $companyName = $companyDetails['data']['company_name'];

            $crid = $data["id"];

            // unserialize customer details
            // $customerDetails = unserialize($data["customerDetails"]);
            // unset($data['customerDetails']);
            $data['companyName'] = $companyName;

            $crcSql = "SELECT * FROM `erp_credit_note_credit` as crc, `erp_credit_notes` as cr WHERE crc.credit_note_id=$crid";
            $crc = queryGet($crcSql, true);
            $crc_data = $crc["data"];

            $crdSql = "SELECT * FROM `erp_credit_note_debit` as crd, `erp_credit_notes` as cr WHERE crd.credit_note_id=$crid";
            $crd = queryGet($crdSql, true);
            $crd_data = $crd["data"];

            $data_array[] = array("cr" => $data, "crc" => $crc_data, "crd" => $crd_data);
        }
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "Data found",
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Not found",
            "data" => []

        ], 400);
    }


}
else{

}

    ?>

    