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
        $cond .= " AND (`debit_note_no` like '%" . $_POST['keyword'] . "%' OR `documentNo` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_debit_notes`   WHERE `company_id`=" . $company_id . " AND `branch_id` = " . $branch_id . " AND `location_id` = " . $location_id . " AND `debitors_type` = 'vendor' AND `party_id`=" . $vendor_id . " " . $cond . " ORDER BY `id` desc limit " . $start . "," . $end . " ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $data) {
            $companyID = $data['company_id'];

            $companyList = "SELECT * FROM `erp_companies`  WHERE `company_id`=" . $companyID . " ";
            $companyDetails = queryGet($companyList);
            $companyName = $companyDetails['data']['company_name'];

            $drid = $data["id"];

            // unserialize customer details
            // $customerDetails = unserialize($data["customerDetails"]);
            // unset($data['customerDetails']);
            // $data['customerDetails'] = $customerDetails;
            $data['companyName'] = $companyName;

            $crcSql = "SELECT * FROM `erp_debit_note_credit` as drc, `erp_debit_notes` as dr WHERE drc.debit_note_id=$drid";
            $crc = queryGet($crcSql, true);
            $drc_data = $crc["data"];

            $crdSql = "SELECT * FROM `erp_debit_note_debit` as drd, `erp_debit_notes` as dr WHERE drd.debit_note_id=$drid";
            $crd = queryGet($crdSql, true);
            $drd_data = $crd["data"];

            $data_array[] = array("dr" => $data, "drc" => $drc_data, "drd" => $drd_data);
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
else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";