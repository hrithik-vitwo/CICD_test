<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];
    $kamId = $authCustomer['kamId'];

    $requestBody = requestBody();
    $claimz_id = $requestBody['user_id'];

    $pageNo = $requestBody['pageNo'];
    $show = $requestBody['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($requestBody['formDate']) && $requestBody['formDate'] != '') {
        $cond .= " AND so.so_date between DATE('" . $requestBody['formDate']."') AND DATE('" . $requestBody['toDate'] . "')";
    }

    if (isset($requestBody['keyword']) && $requestBody['keyword'] != '') {
        $cond .= " AND (so.so_number like '%" . $requestBody['keyword'] . "%' OR so.goodsType like '%" . $requestBody['keyword'] . "%')";
    }

    // $sql_list = "SELECT 
    //                 so.*, 
    //                 uom.label AS soApprovalStatus, 
    //                 customer.trade_name AS customerName 
    //             FROM 
    //                 `erp_branch_sales_order` AS so, 
    //                 `erp_customer` AS customer, 
    //                 `erp_status_master` AS uom 
    //             WHERE 
    //                 so.approvalStatus = uom.code 
    //                 AND so.customer_id = customer.customer_id 
    //                 AND so.company_id = $company_id 
    //                 AND so.claimz_id = $claimz_id OR so.kamId = $kamId" . $cond . " 
    //             ORDER BY so.so_id DESC
    //             LIMIT
    //                 " . $start . ", 
    //                 " . $end . "";
    
    $sql_list = "SELECT
                    so.*,
                    uom.label AS soApprovalStatus,
                    customer.trade_name AS customerName
                FROM
                    `erp_branch_sales_order` AS so
                LEFT JOIN
                    `erp_customer` AS customer ON so.customer_id = customer.customer_id
               LEFT JOIN
                    `erp_status_master` AS uom ON so.approvalStatus = uom.code
                WHERE
                    ((
                        so.company_id = $company_id
                        AND so.claimz_id = $claimz_id
                    )
                    OR so.kamId = $kamId)
                    ".$cond."
                ORDER BY
                    so.so_id DESC
                LIMIT
                    " . $start . ", 
                    " . $end . "";
    $iv_sql = queryGet($sql_list, true);
    // console($iv_sql);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $data) {
            $data_array[] = array("items" => $data);
        }
        sendApiResponse([
            "status"    => $iv_sql['status'],
            "message"   => $iv_sql['message'],
            "data"      => $iv_sql['data']
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "sql" => $sql_list,
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
