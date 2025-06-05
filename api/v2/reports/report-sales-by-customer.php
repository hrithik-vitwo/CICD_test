<?php
require_once("api.php");
require_once("api.report.helper.php");
if (requestMethod() === "POST") {
    $authUser = authUser();
    // {
    //     "company_id": "1",
    //     "branch_id": "1",
    //     "location_id": "1",
    //     "created_by": "4|location",
    //     "updated_by": "4|location",
    //     "authUserId": "4",
    //     "authUserName": "Rachhel Sk",
    //     "authUserEmail": "rachhel@vitwo.in",
    //     "authUserVariant": "91",
    //     "authUserRole": "2",
    //     "authUserType": "location",
    //     "companyCurrency": "2",
    //     "compOpeningDate": "2023-12-31",
    //     "isPoEnabled": "0",
    //     "companyName": "SAREGAMA INDIA LIMITED",
    //     "companyCode": "9231001",
    //     "companyPAN": "AAACT9815B",
    //     "companyCOB": "Pvt Ltd",
    //     "branchName": "Karnataka",
    //     "branchCode": "8231001",
    //     "branchGstin": "29AAACT9815B1ZC",
    //     "locationName": "Karnataka Virgo Nagar",
    //     "locationCode": "7231001",
    //     "locationCity": "Karnataka",
    //     "decimalPlaces": 2
    // }
    // $authUser->company_id, $authUser->branch_id, $authUser->location_id, $authUser->authUserId, $authUser->authUserRole, $authUser->authUserType etc.


    //================================================= [ REQUEST VALIDATION ] =====================================================
    $validObj = validate(requestBody(), [
        "fromDate" => "required",
        "toDate" => "required",
        "typeDate" => "required",
        "page" => "required",
        "pageSize" => "required",
        "compareWith" => "required",
        "numberOfPeriod" => "required",
    ]);

    if ($validObj["status"] != "success") {
        sendApiResponse([
            "status"=>"failed",
            "message"=>"Invalid request data",
            "formError"=>$validObj["errors"],
            "formData"=>requestBody()
        ], 400);
    }
    //================================================= [ END REQUEST VALIDATION ] =====================================================

    //================================================ [ REQUEST DATA EXTRACTION ] =====================================================
    $fromDate = date("Y-m-d", strtotime(requestGet("fromDate", date("Y-m-d"))));    // Report start date
    $toDate = date("Y-m-d", strtotime(requestGet("toDate", date("Y-m-d"))));        // Report end date
    $typeDate = requestGet("typeDate", "custom");                                   // Report date tyoe like custom, week, month, year
    $page = intval(requestGet("page", 1));                                                  // Page number
    $pageSize = intval(requestGet("pageSize", 10));                                         // Page size
    $compareWith = requestGet("compareWith", "previousPeriod");                     // Compare with previous period
    $numberOfPeriod = intval(requestGet("numberOfPeriod", 1));                              // Number of period / bucket
    $reportHelper = new ReportHelper();
    $toDatelList = $reportHelper->generateDateRangeByCompareWith($fromDate, $toDate, $typeDate, $compareWith, $numberOfPeriod);
    //================================================ [ END REQUEST DATA EXTRACTION ] =================================================


    //===================================================== [ GENERATE REPORT ] ========================================================

    $dbObj = new Database();

    $dbObj->queryUpdate("SET
        sql_mode =(
        SELECT
        REPLACE
            (@@sql_mode, 'ONLY_FULL_GROUP_BY', '')
        )");
    $reportData = $dbObj->queryGet("
            SELECT
                location_id,
                invoice_no,
                invoice_date,
                po_date,
                total_tax_amt,
                sub_total_amt,
                all_total_amt,
                itemCode,
                itemName,
                baseUnitMeasure,
                invoiceQty,
                unitPrice,
                customer_code,
                customer_pan,
                customer_gstin,
                trade_name
            FROM
                (
                SELECT
                    invoice.location_id,
                    invoice.invoice_no,
                    invoice.invoice_date,
                    invoice.po_date,
                    invoice.total_tax_amt,
                    invoice.sub_total_amt,
                    invoice.all_total_amt,
                    item.itemCode,
                    item.itemName,
                    item.baseUnitMeasure,
                    inv_item.invoiceQty,
                    inv_item.unitPrice,
                    customer.customer_code,
                    customer.customer_pan,
                    customer.customer_gstin,
                    customer.trade_name
                FROM
                    erp_branch_sales_order_invoices AS invoice
                LEFT JOIN erp_customer AS customer
                ON
                    invoice.customer_id = customer.customer_id
                LEFT JOIN erp_branch_sales_order_invoice_items AS inv_item
                ON
                    invoice.so_invoice_id = inv_item.so_invoice_id
                LEFT JOIN erp_inventory_items AS item
                ON
                    item.itemId = inv_item.inventory_item_id
                WHERE
                    invoice.location_id = 1
            ) AS details_view
            GROUP BY
                customer_code LIMIT $pageSize OFFSET $page", true);



    sendApiResponse([
        "status" => "success",
        "message" => "Successfully production report generated",
        "metadata" => [
            "columns" => [
                [
                    "name" => "Invoice No",
                    "type"=> "string",
                ],
                [
                    "name" => "Invoice Date",
                    "type"=> "date",
                ],
                [
                    "name" => "Customer Code",
                    "type"=> "string",
                ],
                [
                    "name" => "Customer PAN",
                    "type"=> "string",
                ],
                [
                    "name" => "Customer GSTIN",
                    "type"=> "string",
                ],
                [
                    "name" => "Customer Name",
                    "type"=> "string",
                ]
            ],
            "pages"=> [
                "page" => $page,
                "pageSize" => $pageSize,
                "totalPage" => 10,
            ],
        ],
        "data" => $reportData["data"],
    ], 200);

} else {
    sendApiResponse([
        "status" => "failed",
        "message" => "Request method not allowed"
    ], 405);
}
