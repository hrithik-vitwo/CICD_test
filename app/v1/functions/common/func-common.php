<?php

function saveTheQueryLog($query)
{
    if (file_exists("/home/devalpha/public_html/log.txt")) {
        $logFilePath = "/home/devalpha/public_html/log.txt";
    } else if (file_exists("/var/www/one.vitwo.ai/public_html/log.txt")) {
        $logFilePath = "/var/www/one.vitwo.ai/public_html/log.txt";
    } else {
        $logFilePath = "";
    }
    if ($logFilePath != "") {
        $userIP = $_SERVER['REMOTE_ADDR'] ?? "0.0.0.0";
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "";
        $timestamp = date("Y-m-d H:i:s");
        $logMessage = "$userIP - $timestamp - $userAgent - SQL: $query\n";
        file_put_contents($logFilePath, $logMessage, FILE_APPEND);
    }
}


function itemQtyTotalStockChecking($item_id, $stockLoc, $asondate = null)
{

    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    if (empty($asondate)) {
        $asondate = date("Y-m-d");
    }

    // $selStockLog = "SELECT COALESCE(SUM(itemQty), 0) AS itemQty
    // FROM erp_inventory_stocks_log
    // WHERE companyId=$company_id 
    // AND branchId=$branch_id 
    // AND locationId=$location_id 
    // AND storageType IN ($stockLoc)
    // AND itemId = $item_id
    // AND postingDate <= '$asondate' 
    // GROUP BY itemId";
    // $getStockLog = queryGet($selStockLog);
    $returnobj = itemQtyStockChecking($item_id, $stockLoc, '', '', $asondate);
    $getStockLog['data']['itemQty'] = $returnobj['sumOfBatches'] ?? 0;

    return $getStockLog;
}


// add branch SO delivery 

function itemQtyStockCheckWithAcc($item_id, $stockLoc, $ordering = 'ASC', $refNumber = null, $asondate = null)
{
    global $company_id, $branch_id, $location_id;

    $asondate = $asondate ?: date("Y-m-d");
    $refCondition = '';

    if (!empty($refNumber)) {
        $refCondition = "AND CONCAT(log.logRef, log.storageLocationId) IN ($refNumber)";
    }

    $sql = "
        SELECT
            warh.warehouse_id,
            warh.warehouse_code,
            warh.warehouse_name,
            loc.storage_location_id,
            loc.storage_location_code,
            loc.storage_location_name,
            loc.storage_location_type,
            loc.storageLocationTypeSlug,
            (
                SELECT SUM(itemQty)
                FROM erp_inventory_stocks_log
                WHERE
                    storageLocationId = log.storageLocationId
                    AND logRef = log.logRef
                    AND itemId = log.itemId
                    AND companyId = log.companyId
                    AND branchId = log.branchId
                    AND locationId = log.locationId
            ) AS itemQty,
            log.logRef,
            log.bornDate,
            log.refNumber,
            log.refActivityName,
            log.stockLogId,
            MAX(log.itemPrice) AS itemPrice,
            CONCAT(log.logRef, log.storageLocationId) AS logRefConcat
        FROM
            erp_inventory_stocks_log AS log
        LEFT JOIN erp_storage_location AS loc ON log.storageLocationId = loc.storage_location_id
        LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id

        LEFT JOIN erp_grn AS grn ON grn.grnCode = 
            CASE 
                WHEN log.refActivityName IN ('STRGE-LOC','PGI','REV-INVOICE','CN', 'DN', 'MAT-MAT-IN') THEN log.logRef
                ELSE log.refNumber
            END
            AND grn.companyId = $company_id
            AND grn.branchId = $branch_id
            AND grn.locationId = $location_id

        LEFT JOIN erp_production_declarations AS prod ON prod.code = 
            CASE 
                WHEN log.refActivityName IN ('STRGE-LOC', 'PGI', 'REV-INVOICE','CN', 'DN', 'MAT-MAT-IN') THEN log.logRef
                ELSE log.refNumber
            END
            AND prod.company_id = $company_id
            AND prod.branch_id = $branch_id
            AND prod.location_id = $location_id

        WHERE
            log.companyId = $company_id
            AND log.branchId = $branch_id
            AND log.locationId = $location_id
            AND log.itemId = $item_id
            AND loc.storageLocationTypeSlug IN ($stockLoc)
            AND log.storageType IN ($stockLoc)
            AND log.bornDate <= '$asondate'
            $refCondition
            AND (
                (prod.prod_declaration_journal_id IS NOT NULL AND prod.fgsfg_declaration_journal_id IS NOT NULL)
                OR
                (grn.grnCode IS NOT NULL AND grn.grnPostingJournalId IS NOT NULL)
            )

        GROUP BY
            loc.storage_location_id,
            loc.storage_location_code,
            loc.storage_location_name,
            loc.storage_location_type,
            loc.storageLocationTypeSlug,
            log.logRef,
            log.bornDate
        HAVING itemQty > 0
        ORDER BY
            log.postingDate $ordering,
            log.stockLogId $ordering
    ";

    $result = queryGet($sql, true);

    $result['sumOfBatches'] = array_sum(array_column($result['data'], 'itemQty')) ?: 0;
    return $result;
    // return [
    //     'data' => $result,
    //     'sumOfBatches' => array_sum(array_column($result['data'], 'itemQty')) ?: 0
    // ];
}
function itemQtyStockChecking($item_id, $stockLoc, $ordering = 'ASC', $refNumber = null, $asondate = null, $zeroshow = null)
{

    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    if (empty($asondate)) {
        $asondate = date("Y-m-d");
    }
    $cond = '';
    // if (!empty($refNumber)) {
    //     $cond .= " AND log.refNumber IN ($refNumber)";
    // }
    if (!empty($refNumber)) {
        $cond .= " AND CONCAT(log.logRef, log.storageLocationId) IN ($refNumber)";
    }
    $having = ' HAVING itemQty > 0 ';
    if (!empty($zeroshow)) {
        $having = "";
    }
    //$selStockLog = "SELECT loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,SUM(log.itemQty) as itemQty,log.itemUom,log.logRef,grn.postingDate FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_grn AS grn ON log.logRef=grn.grnCode WHERE log.companyId=$company_id AND log.branchId=$branch_id AND log.locationId=$location_id AND log.itemId=$item_id AND grn.postingDate BETWEEN '2023-06-01' AND '" . $today . "' AND loc.storageLocationTypeSlug IN('rmWhOpen','rmWhReserve','fgWhOpen') GROUP BY loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,log.itemUom,log.logRef,grn.postingDate ORDER BY grn.postingDate ASC";
    $selStockLog = "SELECT
            warh.warehouse_id,
            warh.warehouse_code,
            warh.warehouse_name,
            loc.storage_location_id,
            loc.storage_location_code,
            loc.storage_location_name,
            loc.storage_location_type,
            loc.storageLocationTypeSlug,
            (SELECT SUM(itemQty) FROM erp_inventory_stocks_log WHERE storageLocationId=log.storageLocationId AND logRef= log.logRef AND itemId=log.itemId AND companyId=log.companyId AND branchId=log.branchId AND locationId=log.locationId) AS itemQty,
            log.logRef,
            log.bornDate,
            log.refNumber,
            log.refActivityName,
            MAX(log.itemPrice) as itemPrice,
            CONCAT(log.logRef, log.storageLocationId) AS logRefConcat -- Concatenate logRef and storageLocationId
        FROM
            erp_inventory_stocks_log AS log
        LEFT JOIN erp_storage_location AS loc
        ON
            log.storageLocationId = loc.storage_location_id
        LEFT JOIN erp_storage_warehouse AS warh
        ON
            loc.warehouse_id=warh.warehouse_id             
        WHERE
        log.companyId=$company_id 
        AND log.branchId=$branch_id 
        AND log.locationId=$location_id 
        AND log.itemId=$item_id 
        AND loc.storageLocationTypeSlug IN ($stockLoc) 
        AND log.storageType IN ($stockLoc)
        AND log.bornDate <= '$asondate' 
        $cond 
        GROUP BY
            loc.storage_location_id,
            loc.storage_location_code,
            loc.storage_location_name,
            loc.storage_location_type,
            loc.storageLocationTypeSlug,
            log.logRef,
            log.bornDate
        $having
        ORDER BY
            log.bornDate $ordering";


    $getStockLog = queryGet($selStockLog, true);
    // return $getStockLog;

    $totquantities = array_column($getStockLog['data'], "itemQty");
    $itemOpenStocks = array_sum($totquantities);
    if ($itemOpenStocks == '') {
        $itemOpenStocks = '0';
    }
    $getStockLog['sumOfBatches'] = $itemOpenStocks;
    $getStockLog['getStockLogsql'] = $getStockLog;

    return $getStockLog;
}



function convertToWHSLBatchArrayCommon($data)
{
    return array_reduce($data, function ($carry, $item) {
        $warehouse_id = $item['warehouse_id'];
        $storage_location_id = $item['storage_location_id'];

        if (!array_key_exists($warehouse_id, $carry)) {
            $carry[$warehouse_id] = [
                'warehouse_id' => $item['warehouse_id'],
                'warehouse_code' => $item['warehouse_code'],
                'warehouse_name' => $item['warehouse_name'],
                'storage_locations' => []
            ];
        }

        if (!array_key_exists($storage_location_id, $carry[$warehouse_id]['storage_locations'])) {
            $carry[$warehouse_id]['storage_locations'][$storage_location_id] = [
                'storage_location_id' => $item['storage_location_id'],
                'storage_location_code' => $item['storage_location_code'],
                'storage_location_name' => $item['storage_location_name'],
                'storage_location_type' => $item['storage_location_type'],
                'storageLocationTypeSlug' => $item['storageLocationTypeSlug'],
                'batches' => []
            ];
        }

        $carry[$warehouse_id]['storage_locations'][$storage_location_id]['batches'][] = $item;

        return $carry;
    }, []);
}
function generateAuditTrailByMail($POST)
{
    // global $company_id;
    // global $branch_id;
    // global $location_id;
    // global $created_by;
    // global $updated_by;

    // $branch_id = $branch_id ?? '';
    // $location_id = $location_id ?? '';

    $returnData = [];
    $isValidate = validate($POST, [
        "basicDetail" => "required",
        "action_data" => "required"
    ], [
        "basicDetail" => "Required",
        "action_data" => "Required"
    ]);
    //console($POST);
    if ($isValidate["status"] == "success") {

        $trail_type = $POST['basicDetail']['trail_type'] ?? '';
        $table_name = $POST['basicDetail']['table_name'] ?? '';
        $column_name = $POST['basicDetail']['column_name'] ?? '';
        $document_id = $POST['basicDetail']['document_id'] ?? '';
        $party_type = $POST['basicDetail']['party_type'] ?? '';
        $party_id = $POST['basicDetail']['party_id'] ?? '';
        $document_number = $POST['basicDetail']['document_number'] ?? '';
        $action_code = $POST['basicDetail']['action_code'] ?? '';
        $action_referance = $POST['basicDetail']['action_referance'] ?? '';
        $action_title = $POST['basicDetail']['action_title'] ?? '';
        $action_name = $POST['basicDetail']['action_name'] ?? '';
        $action_type = $POST['basicDetail']['action_type'] ?? '';
        $action_url = $POST['basicDetail']['action_url'] ?? '';
        $action_previous_url = $POST['basicDetail']['action_previous_url'] ?? '';
        $action_sqlQuery = $POST['basicDetail']['action_sqlQuery'] ?? '';
        $action_data = serialize($POST['action_data']);
        $others = $POST['basicDetail']['others'] ?? '';
        $remark = $POST['basicDetail']['remark'] ?? '';
        $company_id = $POST['basicDetail']['company_id'] ?? '';
        $branch_id = $POST['basicDetail']['branch_id'] ?? '';
        $location_id = $POST['basicDetail']['location_id'] ?? '';
        $updated_by = $POST['basicDetail']['updated_by'] ?? '';
        $created_by = $POST['basicDetail']['created_by'] ?? '';
        $table_name = "erp_audit_trail_" . $company_id . "_table";
        $insAudit = "INSERT INTO `" . $table_name . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `party_type`='" . $party_type . "',
                                `party_id`='" . $party_id . "',
                                `trail_type`='" . $trail_type . "',
                                `table_name`='" . $table_name . "',
                                `column_name`='" . $column_name . "',
                                `document_id`='" . $document_id . "',
                                `document_number`='" . $document_number . "',
                                `action_code`='" . $action_code . "',
                                `action_referance`='" . $action_referance . "',
                                `action_title`='" . $action_title . "',
                                `action_name`='" . $action_name . "',
                                `action_type`='" . $action_type . "',
                                `action_url`='" . $action_url . "',
                                `action_previous_url`='" . $action_previous_url . "',
                                `action_sqlQuery`='" . $action_sqlQuery . "',
                                `action_data`='" . $action_data . "',
                                `others`='" . $others . "',
                                `remark`='" . $remark . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'";
        $returnData = queryInsert($insAudit);
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    $returnData['POSTAdut'] = $POST;
    return $returnData;
}

function generateAuditTrail($POST)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $branch_id = $branch_id ?? '';
    $location_id = $location_id ?? '';

    $returnData = [];
    $isValidate = validate($POST, [
        "basicDetail" => "required",
        "action_data" => "required"
    ], [
        "basicDetail" => "Required",
        "action_data" => "Required"
    ]);
    //console($POST);
    if ($isValidate["status"] == "success") {

        $trail_type = $POST['basicDetail']['trail_type'] ?? '';
        $table_name = $POST['basicDetail']['table_name'] ?? '';
        $column_name = $POST['basicDetail']['column_name'] ?? '';
        $document_id = $POST['basicDetail']['document_id'] ?? '';
        $party_type = $POST['basicDetail']['party_type'] ?? '';
        $party_id = $POST['basicDetail']['party_id'] ?? '';
        $document_number = $POST['basicDetail']['document_number'] ?? '';
        $action_code = $POST['basicDetail']['action_code'] ?? '';
        $action_referance = $POST['basicDetail']['action_referance'] ?? '';
        $action_title = $POST['basicDetail']['action_title'] ?? '';
        $action_name = $POST['basicDetail']['action_name'] ?? '';
        $action_type = $POST['basicDetail']['action_type'] ?? '';
        $action_url = $POST['basicDetail']['action_url'] ?? '';
        $action_previous_url = $POST['basicDetail']['action_previous_url'] ?? '';
        $action_sqlQuery = $POST['basicDetail']['action_sqlQuery'] ?? '';
        $action_data = serialize($POST['action_data']);
        $others = $POST['basicDetail']['others'] ?? '';
        $remark = $POST['basicDetail']['remark'] ?? '';

        $insAudit = "INSERT INTO `" . ERP_AUDIT_TRAIL . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `party_type`='" . $party_type . "',
                                `party_id`='" . $party_id . "',
                                `trail_type`='" . $trail_type . "',
                                `table_name`='" . $table_name . "',
                                `column_name`='" . $column_name . "',
                                `document_id`='" . $document_id . "',
                                `document_number`='" . $document_number . "',
                                `action_code`='" . $action_code . "',
                                `action_referance`='" . $action_referance . "',
                                `action_title`='" . $action_title . "',
                                `action_name`='" . $action_name . "',
                                `action_type`='" . $action_type . "',
                                `action_url`='" . $action_url . "',
                                `action_previous_url`='" . $action_previous_url . "',
                                `action_sqlQuery`='" . $action_sqlQuery . "',
                                `action_data`='" . $action_data . "',
                                `others`='" . $others . "',
                                `remark`='" . $remark . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'";
        $returnData = queryInsert($insAudit);
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    $returnData['POSTAdut'] = $POST;
    return $returnData;
}


function convertArraysToStrings($array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = convertArraysToStrings($value);
        } else {
            $array[$key] = var_export($value, true);
        }
    }
    return $array;
}

function getUomList($type = 'material')
{
    global $company_id;
    return queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE (`companyId`=$company_id OR `companyId`=0) AND `uomType`='$type' AND uomStatus='active'", true);
}


function getUomDetail($uomId)
{
    global $company_id;
    return queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE uomId=$uomId");
}


function compareArrays($array1, $array2)
{
    $diff = array();

    foreach ($array1 as $key => $value) {
        if (!isset($array2[$key])) {
            $diff[$key] = $value;
        } elseif (is_array($value)) {
            $nestedDiff = compareArrays($value, $array2[$key]);
            if (!empty($nestedDiff)) {
                $diff[$key] = $nestedDiff;
            }
        } elseif ($array2[$key] !== $value) {
            $diff[$key] = $value;
        }
    }

    return $diff;
}

function checkCompanyNames($companyName1 = null, $companyName2 = null)
{
    $rules = ["." => "", "private" => "pvt", "limited" => "ltd", "organization" => "org", "organisation" => "org"];
    $company1 = strtolower($companyName1);
    $company2 = strtolower($companyName2);
    foreach ($rules as $key => $val) {
        $company1 = str_ireplace($key, $val, $company1);
        $company2 = str_ireplace($key, $val, $company2);
    }
    if ($company1 === $company2) {
        return true;
    } else {
        return false;
    }
}

function currency_conversion($source = "INR", $currencies = "USD")
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.apilayer.com/currency_data/live?source=" . $source . "&currencies=" . $currencies,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            "apikey: L5u2YdlnuP6neFjm2DRiCf8w0BqRXiiG"
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}


// function sendNotification($device_tokens, $title, $message, $link = "")
// {
//     try {
//         $SERVER_API_KEY = 'AAAAdwPuc94:APA91bG5p3LzKKZwdBpNFHU9ZZe1yR9hs4qN72TWe2ULkclyJMQ-E_PAo-fKoh-svjC17xhZf61Q9kewP9ISYxdq-sPzQJhIkZExdrG2SFItyt-A_41Z3wEZ0ZZiEp7Alg-E2F7vCFx5';

//         if ($link == "" || $link == null) {
//             $notification = [
//                 "title" => $title,
//                 "message" => $message,
//                 "link" => "https://partneralpha.page.link/signIn"
//             ];
//         } else {
//             $notification = [
//                 "title" => $title,
//                 "message" => $message,
//                 "link" => $link
//             ];
//         }
//         $data = [
//             "to" => $device_tokens, // for multiple device ids
//             "notification" => $notification,
//         ];
//         $dataString = json_encode($data);

//         $headers = [
//             'Authorization: key=' . $SERVER_API_KEY,
//             'Content-Type: application/json',
//         ];

//         $ch = curl_init();

//         curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//         curl_setopt($ch, CURLOPT_POST, true);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
//         $response = curl_exec($ch);
//         curl_close($ch);
//         $data_r['status'] = '1';
//         $data_r['msg'] = 'success';
//         $data_r['data'] = json_decode($response);
//         $response = json_decode($response);
//         return $data_r;
//         // sendApiResponse($data_r, 200);
//         // return response()->json($data_r, 200);
//         //return $response;
//     } catch (\Throwable $th) {
//         //throw $th;
//         $datae['status'] = "0";
//         $datae["msg"] = "failed";
//         $datae["data"] = "something went wrong";
//         // sendApiResponse($datae, 501);
//         // return response()->json($datae, 501);
//         return $datae;
//     }
// }

function sendNotification($device_tokens, $message, $link)
{
    try {
        $SERVER_API_KEY = 'AAAAu3bXmOU:APA91bGoEXBH_nc7dCuefNLt7bWNtDeyai0LREOuJidAJ1tQK4JxhYAjEvwq0nz_iWJyce4gsgjorI9Rkt8hOxCoBibV74mtw1yXuXXGSa5jGQHf2mHJf3v1p_Rhxp4eR7N7CJ1UVruk';


        // payload data, it will vary according to requirement
        $notification = [
            "title" => $message,
            "link" => $link
        ];
        $data = [
            "to" => $device_tokens, // for multiple device ids
            "notification" => $notification,
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        curl_close($ch);
        $data_r['status'] = '1';
        $data_r['msg'] = 'success';
        $data_r['data'] = json_decode($response);
        $response = json_decode($response);
        // sendApiResponse($data_r, 200);
        // return response()->json($data_r, 200);
        //return $response;
    } catch (\Throwable $th) {
        //throw $th;
        $data_r['status'] = "0";
        $data_r["msg"] = "failed";
        $data_r["data"] = "something went wrong";
        // sendApiResponse($datae, 501);
        // return response()->json($datae, 501);
    }
    return $data_r;
}

function postingDateValidation()
{
    global $admin_variant;

    $returnData = [];
    $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
    $check_var_data = $check_var_sql['data'];
    $max = $check_var_data['month_end'];
    $min = $check_var_data['month_start'];
    $month_var = date("m-Y", strtotime($max));

    $currentDate = date("Y-m-d");
    $currentMonthVariant = date("m-Y");

    if ($month_var < $currentMonthVariant) {
        $prev_date = $min;
        $next_date = $max;
        $selected_date = $max;
    } else if ($month_var == $currentMonthVariant) {
        $prev_date = $min;
        $next_date = $currentDate;
        $selected_date = $currentDate;
    } else {
        $prev_date = $min;
        $next_date = $max;
        $selected_date = $max;
    }



    $returnData['status'] = 'success';
    $returnData['start_date'] = $prev_date;
    $returnData['end_date'] = $next_date;
    $returnData['selected_date'] = $selected_date;

    return $returnData;
}



function createCompanyUploadDirs()
{
    $compStorageDir = COMP_STORAGE_DIR;
    $parentDir = dirname($compStorageDir);
    if (is_writable($parentDir)) {
        if (!is_dir($compStorageDir)) {
            mkdir($compStorageDir);
        }
        if (!is_dir($compStorageDir . "/profile")) {
            mkdir($compStorageDir . "/profile");
        }
        if (!is_dir($compStorageDir . "/cancelled-cheque")) {
            mkdir($compStorageDir . "/cancelled-cheque");
        }
        if (!is_dir($compStorageDir . "/grn-invoice")) {
            mkdir($compStorageDir . "/grn-invoice");
        }
        if (!is_dir($compStorageDir . "/visiting-card")) {
            mkdir($compStorageDir . "/visiting-card");
        }
        if (!is_dir($compStorageDir . "/acc-statement")) {
            mkdir($compStorageDir . "/acc-statement");
        }
        if (!is_dir($compStorageDir . "/payment")) {
            mkdir($compStorageDir . "/payment");
        }
        if (!is_dir($compStorageDir . "/collection")) {
            mkdir($compStorageDir . "/collection");
        }
        if (!is_dir($compStorageDir . "/others")) {
            mkdir($compStorageDir . "/others");
        }
        return [
            "status" => "success",
            "message" => "All dir creation was successful"
        ];
    } else {
        return [
            "status" => "warning",
            "message" => "Dir is not created, please provide writable permissions to '" . $parentDir . "' folder"
        ];
    }
}


function create_log($query_sql = null, $data = array(), $readystmnt = '', $tableName = null)
{
    global $dbCon;
    $prepareData         =    serialize($data);
    $query_type = 'Tset';
    $execute_query = $query_sql;
    $val = mysqli_query($dbCon, 'select 1 from `' . $tableName . '_log` LIMIT 1');
    if ($val !== TRUE) {
        $sql    =    "CREATE TABLE IF NOT EXISTS `" . $tableName . "_log` (
							  `id` int(20) NOT NULL AUTO_INCREMENT,
							  `date` varchar(255) DEFAULT NULL,
							  `ipAddress` varchar(255) DEFAULT NULL,
							  `tableName` varchar(255) DEFAULT NULL,
							  `primary_id` int(20) DEFAULT NULL,
							  `type` varchar(255) DEFAULT NULL,
							  `query` varchar(255) DEFAULT NULL,
							  `prepareData` text DEFAULT NULL,
							  `userId` varchar(255) DEFAULT NULL,
							  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
							  `createdDate` datetime DEFAULT NULL DEFAULT current_timestamp(),
							  `createdIp` varchar(255) DEFAULT NULL,
							  `createdSessionId` varchar(255) DEFAULT NULL,
							  `modifiedDate` datetime DEFAULT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
							  `modifiedIp` varchar(255) DEFAULT NULL,
							  `modifiedSessionId` varchar(255) DEFAULT NULL,
							  PRIMARY KEY (`id`)
							) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

        mysqli_query($dbCon, $sql);
    }

    $sqlInsert    =    "INSERT INTO `" . $tableName . "_log`
							SET
							`date`				=	'" . date('Y-m-d') . "',
							`ipAddress`			=	'" . $_SERVER['REMOTE_ADDR'] . "',
							`type`				=	'" . $query_type . "',
							`query`				=	'" . $execute_query . "',
							`prepareData`		=	'" . $prepareData . "',
							`remarks`			=	'QUERY EXECUTED RECORD',
							`userId`			=	'" . $_SESSION['login_id'] . "',
							`createdSessionId`	=	'" . session_id() . "'";

    mysqli_query($dbCon, $sqlInsert);
}

//*************************************/UPDATE/INSERT - TABLE SETTINGS/******************************************//
function updateInsertTableSettings($POST, $adminId)
{
    global $dbCon;
    $isValidate = count($_POST['settingsCheckbox']);

    if ($isValidate >= 5) {

        $tablename = $POST["tablename"];
        $pageTableName = $POST["pageTableName"];
        $settingsCheckbox = serialize($POST["settingsCheckbox"]);

        $sql = "SELECT * FROM `" . $tablename . "` WHERE `pageTableName`='" . $pageTableName . "' AND `createdBy`='" . $adminId . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $updt = "UPDATE `" . $tablename . "` 
                            SET
                                `pageTableName`='" . $pageTableName . "',
                                `settingsCheckbox`='" . $settingsCheckbox . "',
                                `updatedBy`='" . $adminId . "'
							 WHERE `pageTableName`='" . $pageTableName . "'
							 	AND `createdBy`='" . $adminId . "'";

                if (mysqli_query($dbCon, $updt)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Modified successfully";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Modified failed";
                }
            } else {
                $ins = "INSERT INTO `" . $tablename . "` 
							SET
								`pageTableName`='" . $pageTableName . "',
                                `settingsCheckbox`='" . $settingsCheckbox . "',
                                `updatedBy`='" . $adminId . "',
                                `createdBy`='" . $adminId . "'";
                if (mysqli_query($dbCon, $ins)) {
                    $returnData["status"] = "success";
                    $returnData["message"] = "Modified successfully.";
                } else {
                    $returnData["status"] = "warning";
                    $returnData["message"] = "Modify failed!";
                }
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Please Check Atlast 5";
    }
    return $returnData;
}

function updateInsertTableSettingsCheckbox($POST, $adminId)
{
    global $dbCon;
    $isValidate = count($POST['settingsCheckbox']);

    if ($isValidate >= 5) {

        $tablename = $POST["tablename"];
        $pageTableName = $POST["pageTableName"];
        $settingsCheckbox = serialize($POST["settingsCheckbox"]);

        $sql = "SELECT * FROM `" . $tablename . "` WHERE `pageTableName`='" . $pageTableName . "' AND `createdBy`='" . $adminId . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $updt = "UPDATE `" . $tablename . "` 
                            SET
                                `pageTableName`='" . $pageTableName . "',
                                `settingsCheckbox`='" . $settingsCheckbox . "',
                                `updatedBy`='" . $adminId . "'
							 WHERE `pageTableName`='" . $pageTableName . "'
							 	AND `createdBy`='" . $adminId . "'";

                if (mysqli_query($dbCon, $updt)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Modified successfully";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Modified failed";
                }
            } else {
                $ins = "INSERT INTO `" . $tablename . "` 
							SET
								`pageTableName`='" . $pageTableName . "',
                                `settingsCheckbox`='" . $settingsCheckbox . "',
                                `updatedBy`='" . $adminId . "',
                                `createdBy`='" . $adminId . "'";
                if (mysqli_query($dbCon, $ins)) {
                    $returnData["status"] = "success";
                    $returnData["message"] = "Modified successfully.";
                } else {
                    $returnData["status"] = "warning";
                    $returnData["message"] = "Modify failed!";
                }
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Please Check Atlast 5";
        $returnData['isValidate'] = $isValidate;
        $returnData['POST'] = $POST;
    }
    return $returnData;
}


function getTableSettings($tablename, $pageTableName, $adminId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . $tablename . "` WHERE `pageTableName`='" . $pageTableName . "' AND `createdBy`='" . $adminId . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $sql2 = "SELECT * FROM `" . $tablename . "` WHERE `pageTableName`='" . $pageTableName . "' AND `createdBy`='0'";
            if ($res2 = mysqli_query($dbCon, $sql2)) {
                if (mysqli_num_rows($res2) > 0) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Data found2";
                    $returnData['data'] = mysqli_fetch_all($res2, MYSQLI_ASSOC);
                } else {
                    $settingsCheckbox = 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}';
                    $ins = "INSERT INTO `" . $tablename . "` 
							SET
								`pageTableName`='" . $pageTableName . "',
                                `settingsCheckbox`='" . $settingsCheckbox . "',
                                `updatedBy`='0',
                                `createdBy`='0'";
                    mysqli_query($dbCon, $ins);
                    $sql3 = "SELECT * FROM `" . $tablename . "` WHERE `pageTableName`='" . $pageTableName . "' AND `createdBy`='0'";
                    $res3 = mysqli_query($dbCon, $sql3);
                    if (mysqli_num_rows($res3) > 0) {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Data found2";
                        $returnData['data'] = mysqli_fetch_all($res3, MYSQLI_ASSOC);
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Data not found2";
                        $returnData['data'] = [];
                    }
                }
            } else {
                $returnData['status'] = "danger";
                $returnData['message'] = "Somthing went wrong2";
                $returnData['data'] = [];
            }
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong1";
        $returnData['data'] = [];
    }
    return $returnData;
}

//*****************************************************************************************//


//*************************************/UPDATE/INSERT - TABLE Dashboard/******************************************//
function updateInsertDashTableSettings($POST)
{
    global $dbCon;
    global $created_by;

    $tablename = 'tbl_dashboard_tablesettings';
    $chartName = $POST["chart"];
    $components = serialize($POST);

    $sql = "SELECT * FROM `" . $tablename . "` WHERE `chartName`='" . $chartName . "' AND `createdBy`='" . $created_by . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $updt = "UPDATE `" . $tablename . "` 
                        SET
                            `chartName`='" . $chartName . "',
                            `components`='" . $components . "',
                            `updatedBy`='" . $created_by . "'
                            WHERE `chartName`='" . $chartName . "'
                            AND `createdBy`='" . $created_by . "'";

            $returnData = queryUpdate($updt);
        } else {
            $ins = "INSERT INTO `" . $tablename . "` 
                        SET
                            `chartName`='" . $chartName . "',
                            `components`='" . $components . "',
                            `updatedBy`='" . $created_by . "',
                            `createdBy`='" . $created_by . "'";

            $returnData = queryInsert($ins);
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
    }
    return $returnData;
}


function getDashTableSettings($chartName = NULL)
{
    global $created_by;
    $returnData = [];
    if ($chartName == NULL) {
        $sql = "SELECT * FROM `tbl_dashboard_tablesettings` WHERE `createdBy`='" . $created_by . "'";
        $returnData = queryGet($sql, true);
    } else {
        $sql = "SELECT * FROM `tbl_dashboard_tablesettings` WHERE `chartName`='" . $chartName . "' AND `createdBy`='" . $created_by . "'";
        $returnData = queryGet($sql);
    }
    return $returnData;
}

//*****************************************************************************************//

function WordLimiter($text, $limit)
{
    $explode = explode(' ', $text);
    $string  = '';
    $dots = '...';
    if (count($explode) <= $limit) {
        $dots = '';
        for ($i = 0; $i < count($explode); $i++) {
            $string .= $explode[$i] . " ";
        }
    } else {

        for ($i = 0; $i < $limit; $i++) {

            $string .= $explode[$i] . " ";
        }
    }

    return $string . $dots;
}

function getDateByAddedDays($date, $days = 0)
{
    $tempDate = date_create($date);
    date_add($tempDate, date_interval_create_from_date_string($days . " days"));
    return date_format($tempDate, "Y-m-d");
}

function dateDifference($date_1, $date_2)
{
    $dateFrom    = strtotime($date_1); // or your date as well
    $dateTo        = strtotime($date_2);
    $datediff     = $dateTo - $dateFrom;
    return floor($datediff / (60 * 60 * 24));
}

function formatPrice($price)
{
    if ($price != '')
        return number_format($price, 2);
    else
        return 0.00;
}

function formatDateWeb($date)
{
    global $cfg, $mycms, $mycommoncms;
    if ($date != '') {
        return date('d-m-Y', strtotime($date));
    } else {
        return '';
    }
}

function formatDate($date)
{
    if ($date != '') {
        return date('M j, Y', strtotime($date));
    } else {
        return '';
    }
}

function formatTime($time)
{
    return date('h:i A', strtotime($time));
}

function formatDateTime($datetime)
{
    if ($datetime != '') {
        return date('M j, Y h:i A', strtotime($datetime));
    } else {
        return '';
    }
}

function getNoOfDays($fromdate, $todate)
{
    $start        =    strtotime($fromdate);
    $end        =    strtotime($todate);
    $noOfDays    =    ceil(abs($end - $start) / 86400);
    return $noOfDays;
}

function number_to_words_indian_rupees($num)
{
    $ones = array(
        0 => "ZERO",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN"
    );

    $tens = array(
        2 => "TWENTY",
        3 => "THIRTY",
        4 => "FORTY",
        5 => "FIFTY",
        6 => "SIXTY",
        7 => "SEVENTY",
        8 => "EIGHTY",
        9 => "NINETY"
    );

    $thousands = array(
        0 => "",
        1 => "THOUSAND",
        2 => "LAKH",
        3 => "CRORE"
    );

    $decimal = round($num - intval($num), 2) * 100;
    $num = intval($num);
    $words = "";

    if ($num == 0) {
        $words = $ones[$num];
    } else {
        $crore = intval($num / 10000000);
        $num %= 10000000;
        $lakh = intval($num / 100000);
        $num %= 100000;
        $thousand = intval($num / 1000);
        $num %= 1000;
        $hundreds = intval($num / 100);
        $num %= 100;

        if ($crore > 0) {
            $words .= number_to_words_indian_rupees($crore) . " CRORE ";
        }
        if ($lakh > 0) {
            $words .= number_to_words_indian_rupees($lakh) . " LAKH ";
        }
        if ($thousand > 0) {
            $words .= number_to_words_indian_rupees($thousand) . " THOUSAND ";
        }
        if ($hundreds > 0) {
            $words .= $ones[$hundreds] . " HUNDRED ";
        }
        if ($num > 0) {
            if ($words != "") {
                $words .= "AND ";
            }
            if ($num < 20) {
                $words .= $ones[$num];
            } else {
                $words .= $tens[intval($num / 10)];
                $num %= 10;
                if ($num > 0) {
                    $words .= "-" . $ones[$num];
                }
            }
        }
    }

    if ($decimal > 0) {
        $words .= " AND " . number_to_words_indian_rupees($decimal) . " PAISE";
    }

    return $words;
}

function formatDateORDateTime($datetime, $retunType = null)
{
    global $cfg, $mycms;
    if ($datetime != '') {
        $datetime = str_replace('/', '-', $datetime);
        $date_time = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);

        if ($date_time !== false && !array_sum($date_time->getLastErrors())) {
            // time is set
            if (!empty($retunType)) {
                return date('M j, Y h:i A', strtotime($datetime));
            } else {
                return date('d-m-Y H:i:s', strtotime($datetime));
            }
        } else {
            // time is not set
            if (!empty($retunType)) {
                return date('M j, Y', strtotime($datetime));
            } else {
                return date('d-m-Y', strtotime($datetime));
            }
        }
    } else {
        return '-';
    }
}


function getAge($then)
{
    $then_ts = strtotime($then);
    $then_year = date('Y', $then_ts);
    $age = date('Y') - $then_year;
    if (strtotime('+' . $age . ' years', $then_ts) > time()) $age--;
    return $age;
}

function randomNumber($length = 6, $seeds = 'alphanum')
{
    // Possible seeds
    $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
    $seedings['numeric'] = '0123456789';
    $seedings['alphanum'] = 'abcdefghijkl0123456789mnopqrstuvwxyz';
    $seedings['hexidec'] = '0123456789abcdef';
    // Choose seed
    if (isset($seedings[$seeds])) {
        $seeds = $seedings[$seeds];
    }
    // Seed generator
    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    mt_srand($seed);

    // Generate
    $str = '';
    $seeds_count = strlen($seeds);

    for ($i = 0; $length > $i; $i++) {
        //$str .= $seeds{mt_rand(0, $seeds_count - 1)};
    }
    return $str;
}



function getTimeFormat($time)
{
    if ($time == '00:00' || $time == '00:00:00') {
        $tm = '12:00 AM';
    } else {
        $tms = array();
        $tms = explode(':', $time);
        if ($tms[0] == 12) {
            $tms1 = $tms[0];
            $tm = $tms1 . ':' . $tms[1] . ' PM';
        }
        if ($tms[0] > 12) {
            $tms1 = $tms[0] - 12;
            $tm = $tms1 . ':' . $tms[1] . ' PM';
        }
        if ($tms[0] < 12) {
            $tms1 = $tms[0];
            $tm = $tms1 . ':' . $tms[1] . ' AM';
        }
    }
    return $tm;
}

function number_pad($number, $places)
{
    return str_pad((int) $number, $places, "0", STR_PAD_LEFT);
}

function convertNumberToWords($number)
{

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convertNumberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convertNumberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convertNumberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convertNumberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

//************************************************** */

function getSetAlertMessage($data = [])
{
    if (isset($data["status"]) && isset($data["message"])) {
        $_SESSION["alertMessage"]["status"] = $data["status"];
        $_SESSION["alertMessage"]["message"] = $data["message"];
    } else {
        $returnData = [];
        if (isset($_SESSION["alertMessage"])) {
            $returnData = $_SESSION["alertMessage"];
            unset($_SESSION["alertMessage"]);
        }
        return $returnData;
    }
    return 1;
}

function console($data = null)
{
    if ($data != null) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}


function redirect($url = null)
{
    if ($url != null) {
?>
        <script>
            window.location.href = `<?= $url ?>`;
        </script>
        <?php
    }
}

function swalToast($icon = null, $title = null, $url = null)
{
    if ($icon != null && $title != null) {
        if ($url != null) {
        ?>
            <script>
                $(document).ready(function() {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: `<?= $icon ?>`,
                        title: `&nbsp;<?= $title ?>`
                    }).then(function() {
                        window.location.href = `<?= $url ?>`;
                    });
                });
            </script>
        <?php
        } else { ?>
            <script>
                $(document).ready(function() {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: `<?= $icon ?>`,
                        title: `&nbsp;<?= $title ?>`
                    });
                });
            </script>

        <?php  }
    }
}

function swalAlert($icon = null, $title = null, $text = null, $url = null)
{
    if ($icon != null && $text != null) {
        if ($url != null) {
        ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: `<?= $icon ?>`,
                        title: `<?= $title ?>`,
                        text: `<?= $text ?>`,
                    }).then(function() {
                        window.location.href = `<?= $url ?>`;
                    });
                });
            </script>
        <?php
        } else {
        ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: `<?= $icon ?>`,
                        title: `<?= $title ?>`,
                        text: `<?= $text ?>`,
                    });
                });
            </script>
        <?php
        }
    }
}



function swalConfirmAlert($icon = null, $title = 'Are you sure?', $text = null, $url = null)
{
    if ($icon != null && $text != null) {
        if ($url != null) {
        ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: `warning`,
                        title: `<?= $title ?>`,
                        text: `<?= $text ?>`,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Confirm'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `<?= $url ?>`;
                        }
                    });
                });
            </script>
        <?php
        } else {
        ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: `<?= $icon ?>`,
                        title: `<?= $title ?>`,
                        text: `<?= $text ?>`,
                    });
                });
            </script>
        <?php
        }
    }
}

function uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)
{


    if (!is_writable($dir)) {
        return [
            "status" => "error",
            "message" => "Directory not writable, please set writable permissions" . $dir,
            "data" => ""
        ];
        exit();
    }

    $validationError = "";
    $fileExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $fileNewName = time() . rand(10000, 99999) . "." . $fileExtension;
    if (sizeof($allowedExtensions) > 0) {
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            $validationError = "Extension not allowed";
        }
    }
    if ($file["size"] <= 0) {
        $validationError = "Invalid file" . $file["size"];
    }
    if ($maxSize > 0) {
        if ($file["size"] > $maxSize) {
            $validationError = "File size should be less then " . number_format($maxSize / 1024, 0) . " kb";
        }
    }
    if ($minSize > 0) {
        if ($file["size"] < $minSize) {
            $validationError = "File size should be grater then " . number_format($minSize / 1024, 0) . " kb";
        }
    }
    //upload
    if ($validationError == "") {
        if (move_uploaded_file($file["tmp_name"], $dir . $fileNewName)) {
            $returnData["status"] = "success";
            $returnData["message"] = "Upload success";
            $returnData["data"] = $fileNewName;
        } else {
            $returnData["status"] = "error";
            $returnData["message"] = "Upload fail";
            $returnData["data"] = "";
        }
    } else {
        $returnData["status"] = "error";
        $returnData["message"] = $validationError;
        $returnData["data"] = "";
    }
    return $returnData;
}


function getRandCodeNotInTable($tablename, $fildName)
{
    global $dbCon;
    $rand = rand(11111111, 99999999);
    $sql = "SELECT * FROM `" . $tablename . "` WHERE `" . $fildName . "`='" . $rand . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            getRandCodeNotInTable($tablename, $fildName);
            $returnData['status'] = "warning";
            $returnData['message'] = "Data found";
            $returnData['data'] = '';
        } else {
            $returnData['status'] = "success";
            $returnData['message'] = "Data not found";
            $returnData['data'] = $rand;
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = '';
    }
    return $returnData;
}

function getAllCurrencyType()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_CURRENCY_TYPE . "` WHERE `currency_status`='active' ORDER BY `currency_id` ASC";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

function getAllLanguage()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_LANGUAGE . "` WHERE `language_status`='active' ORDER BY `language_id` ASC";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}


function addNewAdministratorUserGlobal($POST = [], $adminrole = null)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)"
    ]);

    if ($isValidate["status"] == "success") {

        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        if (!empty($adminrole)) {
            $adminRole = $adminrole;
        } else {
            $adminRole = 1;
        }
        $tableName = $POST["tablename"];


        $sql = "SELECT * FROM `" . $tableName . "` WHERE `fldAdminEmail`='" . $adminEmail . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `" . $tableName . "`
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminRole`='" . $adminRole . "'";
                if (isset($POST["fldAdminCompanyId"])) {
                    $fldAdminCompanyId = $POST["fldAdminCompanyId"];
                    $ins .= ", `fldAdminCompanyId`='" . $fldAdminCompanyId . "'";
                }
                if (isset($POST["vendorCode"])) {
                    $vendorCode = $POST["vendorCode"];
                    $ins .= ", `vendorCode`='" . $vendorCode . "'";
                }
                if (isset($POST["customer_code"])) {
                    $customer_code = $POST["customer_code"];
                    $ins .= ", `customer_code`='" . $customer_code . "'";
                }
                if (isset($POST["company_id"])) {
                    $company_id = $POST["company_id"];
                    $ins .= ", `company_id`='" . $company_id . "'";
                }
                if (isset($POST["customer_id"])) {
                    $customer_id = $POST["customer_id"];
                    $ins .= ", `customer_id`='" . $customer_id . "'";
                }
                if (isset($POST["fldAdminBranchId"])) {
                    $fldAdminBranchId = $POST["fldAdminBranchId"];
                    $ins .= ", `fldAdminBranchId`='" . $fldAdminBranchId . "'";
                }
                if (isset($POST["fldAdminVendorId"])) {
                    $fldAdminVendorId = $POST["fldAdminVendorId"];
                    $ins .= ", `fldAdminVendorId`='" . $fldAdminVendorId . "'";
                }
                if (isset($POST["fldAdminCustomerId"])) {
                    $fldAdminCustomerId = $POST["fldAdminCustomerId"];
                    $ins .= ", `fldAdminCustomerId`='" . $fldAdminCustomerId . "'";
                }

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin already exist";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    // return $returnData;
}

function queryGet($query, $isMultipleRows = false)
{
    global $dbCon;
    $returnData = [];
    if ($res = mysqli_query($dbCon, $query)) {
        $numRows = mysqli_num_rows($res);
        if ($numRows > 0) {
            $returnData = [
                "status" => "success",
                "message" => ($numRows > 1) ? $numRows . " records found successfully" : $numRows . "record found successfully",
                "numRows" => $numRows,
                "data" => ($isMultipleRows) ? mysqli_fetch_all($res, MYSQLI_ASSOC) : mysqli_fetch_assoc($res),
                "sql" => $query
            ];
        } else {
            $returnData = [
                "status" => "warning",
                "message" => "Record not found",
                "numRows" => 0,
                "query" => $query,
                "data" => []
            ];
        }
    } else {
        $returnData = [
            "status" => "failed",
            "message" => "Something went wrong, try again later",
            "numRows" => 0,
            "query" => $query,
            "data" => []
        ];
    }

    saveTheQueryLog($query);
    return $returnData;
}


function queryGetNumRows($query)
{
    global $dbCon;
    $returnData = [];
    if ($res = mysqli_query($dbCon, $query)) {
        $numRows = mysqli_num_rows($res);
        if ($numRows > 0) {
            $returnData = [
                "status" => "success",
                "message" => ($numRows > 1) ? $numRows . " records found successfully" : $numRows . "record found successfully",
                "numRows" => $numRows
            ];
        } else {
            $returnData = [
                "status" => "warning",
                "message" => "Record not found",
                "numRows" => 0,
                "query" => $query
            ];
        }
    } else {
        $returnData = [
            "status" => "failed",
            "message" => "Something went wrong, try again later",
            "numRows" => 0,
            "query" => $query
        ];
    }

    saveTheQueryLog($query);
    return $returnData;
}

function queryInsert($query)
{
    global $dbCon;
    $returnData = [];
    if ($res = mysqli_query($dbCon, $query)) {
        $returnData = [
            "status" => "success",
            "message" => "Data saved successfully",
            "insertedId" => mysqli_insert_id($dbCon),
        ];
    } else {
        $returnData = [
            "status" => "failed",
            "message" => "Data saved failed, try again later",
            "insertedId" => "",
            "query" => $query
        ];
    }

    saveTheQueryLog($query);
    return $returnData;
}

function queryUpdate($query)
{
    global $dbCon;
    $returnData = [];
    if ($res = mysqli_query($dbCon, $query)) {
        $returnData = [
            "status" => "success",
            "message" => "Data modified successfully",
        ];
    } else {
        $returnData = [
            "status" => "failed",
            "message" => "Data modified failed, try again later",
            "query" => $query
        ];
    }


    saveTheQueryLog($query);
    return $returnData;
}

function queryDelete($query)
{
    global $dbCon;
    $returnData = [];
    if ($res = mysqli_query($dbCon, $query)) {
        $returnData = [
            "status" => "success",
            "message" => "Data deleted successfully",
        ];
    } else {
        $returnData = [
            "status" => "failed",
            "message" => "Data deleted failed, try again later",
            "query" => $query
        ];
    }


    saveTheQueryLog($query);
    return $returnData;
}

function getSingleCurrencyType($currencyId)
{
    global $dbCon;
    $returnData = '';
    $sql = "SELECT * FROM `" . ERP_CURRENCY_TYPE . "` WHERE `currency_id`=$currencyId";
    $returnData = queryGet($sql)['data']['currency_name'];
    return $returnData;
}


function getCreatedByUser($value)
{
    global $company_id;
    $userFullName = '';
    if (!empty($value)) {
        if (strpos($value, "|")) {
            $valueExpo = explode('|', $value);
            $uId = $valueExpo[0];
            $lavel = $valueExpo[1];

            if ($lavel == 'company') {
                //Fetch Data form company User Table
                $sql = "SELECT * FROM `tbl_company_admin_details` WHERE fldAdminKey=$uId AND fldAdminCompanyId=$company_id LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else if ($lavel == 'branch') {
                //Fetch Data form branch User Table
                $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE fldAdminKey=$uId AND fldAdminCompanyId=$company_id LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else if ($lavel == 'location') {
                //Fetch Data form branch User Table
                $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE fldAdminKey=$uId AND fldAdminCompanyId=$company_id LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else if ($lavel == 'Performer') {
                $sql = "SELECT * FROM `erp_bug_user_details` WHERE fldAdminKey=$uId LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else {
            }
        } else {
            $uId = $value;
            if (isset($_SESSION["logedCompanyAdminInfo"])) {
                //Fetch Data form company User Table
                $sql = "SELECT * FROM `tbl_company_admin_details` WHERE fldAdminKey=$uId AND fldAdminCompanyId=$company_id LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else if (isset($_SESSION["logedBranchAdminInfo"])) {
                //Fetch Data form branch User Table
                $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE fldAdminKey=$uId AND fldAdminCompanyId=$company_id LIMIT 1";
                $query = queryGet($sql);
                $userFullName = $query['data']['fldAdminName'] ?? '-';
            } else {
                $userFullName = 'NULL';
            }
        }
    } else {
        $userFullName = 'NULL';
    }

    return $userFullName;
}

function getAdminUserIdByName($name)
{
    global $company_id;

    $sql = "
    SELECT fldAdminName, CONCAT(fldAdminKey, '|company') AS fldAdminKey
    FROM tbl_company_admin_details 
    WHERE fldAdminCompanyId=$company_id AND fldAdminName LIKE '%" . $name . "%'
    UNION ALL
    SELECT fldAdminName, CONCAT(fldAdminKey, '|branch') AS fldAdminKey
    FROM tbl_branch_admin_details 
    WHERE fldAdminCompanyId=$company_id AND fldAdminName LIKE '%" . $name . "%'
    UNION ALL
    SELECT fldAdminName, CONCAT(fldAdminKey, '|location') AS fldAdminKey
    FROM tbl_branch_admin_details 
    WHERE fldAdminCompanyId=$company_id AND fldAdminName LIKE '%" . $name . "%'
    ";

    $sqlQuery = queryGet($sql, true);

    $fldAdminKey = array_column($sqlQuery['data'], 'fldAdminKey');
    $resultList = implode(' , ', array_map(function ($fldAdminKey) {
        return "'$fldAdminKey'";
    }, $fldAdminKey));

    return $resultList;
}

function getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item_id, $document_no)
{
    $batchSql = "SELECT stockLog.logRef, SUM(stockLog.itemQty)*-1 AS itemQty FROM `erp_inventory_stocks_log` AS stockLog WHERE stockLog.companyId = $company_id AND stockLog.branchId = $branch_id AND stockLog.locationId=$location_id AND stockLog.itemId = '" . $item_id . "' AND stockLog.refNumber = '" . $document_no . "' GROUP BY stockLog.logRef, stockLog.itemQty;";

    $batchQuery = queryGet($batchSql, true);
    $batchString = "";

    if ($batchQuery['status'] == 'success') {
        // $batch = $batchQuery['data'];

        $logRefs = array_column($batchQuery['data'], 'logRef');
        $itemQtys = array_column($batchQuery['data'], 'itemQty');

        $batchString = implode(' || ', array_map(function ($logRef, $itemQty) {
            return "$logRef: $itemQty";
            // return "$logRef";

        }, $logRefs, $itemQtys));

        $batchQuery['batchString'] = $batchString;
    }

    return $batchQuery;
}


//---------------------ItemSerialNumberStart------------------
/*
RM:         11000001 - 11999999
SFG:        12000001 - 12999999
FG:         22000001 - 22999999
SERVICES:   33000001 - 33999999
SERVICESP:  44000001 - 44999999
ASSET:      19000001 - 19999999
*/

function getItemSerialNumber($lastsl, $ItemType)
{
    if ($ItemType == 'RM') {
        $prefix = 11;
    } else if ($ItemType == 'SFG') {
        $prefix = 12;
    } else if ($ItemType == 'FG') {
        $prefix = 22;
    } else if ($ItemType == 'FGT') {
        $prefix = 21;
    } else if ($ItemType == 'SERVICES') {
        $prefix = 33;
    } else if ($ItemType == 'SERVICEP') {
        $prefix = 44;
    } else if ($ItemType == 'ASSET') {
        $prefix = 19;
    } else {
        $prefix = 13;
    }

    if (!empty($lastsl)) {
        $prefixcode = substr($lastsl, 0, 2);
        if ($prefixcode == $prefix) {
            $sl = substr($lastsl, 2);
        } else {
            $sl = 0;
        }
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $code_no = $prefix . str_pad($id, 6, 0, STR_PAD_LEFT);
}
//---------------------ItemSerialNumberEnd------------------

//---------------------SoSerialNumberStart------------------
function getSoSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'SO' . $y . $m;
    $old_prefix = substr($lastsl, 0, 6);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 6);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'SO' . $y . $m . str_pad($id, 3, 0, STR_PAD_LEFT);
}
//---------------------SoSerialNumberEnd------------------

//---------------------SoInvoiceSerialNumberStart------------------
function getSoInvoiceSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'INV';
    $old_prefix = substr($lastsl, 0, 3);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -8);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'INV' . str_pad($id, 8, 0, STR_PAD_LEFT);
}
//---------------------SoInvoiceSerialNumberEnd------------------



//---------------------SoInvoiceSerialNumberByVerientStart------------------
function getInvoiceNumberByVerient($vid)
{
    global $company_id;
    global $admin_variant;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_iv_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_iv_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);
    // date checker
    $check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
    $check_var_data = $check_var_sql['data'];

    $min = $check_var_data['month_start'];
    // Assuming $min contains the date string "2023-06-01"
    $dateParts = explode("-", $min);

    // Extract year, month, and day
    $yearEx = $dateParts[0];  // "2023"
    $monthEx = $dateParts[1]; // "06"
    $dayEx = $dateParts[2];   // "01"

    // To get a date format like "2023-06"
    $dateWithoutDay = $yearEx . "-" . $monthEx;

    $ym = $dateWithoutDay ? $dateWithoutDay : date('Y-m');
    $y = $yearEx ? $yearEx : date('Y'); // Current Year
    $m = $monthEx ? $monthEx : date('m'); // Current Month

    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            // $m = date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            // $y = date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            // $ym = date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                // $y = date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                // $ym = date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $ivVerientupdate = queryUpdate("UPDATE `erp_iv_varient` SET `last_inv_no` = '" . $finalArrySerliz . "' WHERE `id` =" . $iv_varient['data']['id'] . "");
    $responseData = [
        "status" => "success",
        "message" => "Invoice Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" =>  $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------SoInvoiceSerialNumberByVerientEnd------------------



//---------------------getInvoiceNumberByVerientViewStart------------------
function getInvoiceNumberByVerientView($vid)
{
    global $company_id;
    global $admin_variant;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_iv_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_iv_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);

    // date checker
    $check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
    $check_var_data = $check_var_sql['data'];

    $min = $check_var_data['month_start'];
    // Assuming $min contains the date string "2023-06-01"
    $dateParts = explode("-", $min);

    // Extract year, month, and day
    $yearEx = $dateParts[0];  // "2023"
    $monthEx = $dateParts[1]; // "06"
    $dayEx = $dateParts[2];   // "01"

    // To get a date format like "2023-06"
    $dateWithoutDay = $yearEx . "-" . $monthEx;


    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            $m = $monthEx ? $monthEx : date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            $y = $yearEx ? $yearEx : date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            $ym = $dateWithoutDay ? $dateWithoutDay : date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                $y = $yearEx ? $yearEx : date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                $ym = $dateWithoutDay ? $dateWithoutDay : date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $responseData = [
        "status" => "success",
        "message" => "Invoice Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" => $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------getInvoiceNumberByVerientViewEnd------------------



//---------------------DNSerialNumberByVerientStart------------------
function getDNNumberByVerient($vid)
{
    global $company_id;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_dn_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_dn_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);

    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            $m = date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            $y = date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            $ym = date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                $y = date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                $ym = date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $ivVerientupdate = queryUpdate("UPDATE `erp_dn_varient` SET `last_inv_no` = '" . $finalArrySerliz . "' WHERE `id` =" . $iv_varient['data']['id'] . "");
    $responseData = [
        "status" => "success",
        "message" => "DN Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" =>  $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------DNSerialNumberByVerientEnd------------------



//---------------------DNNumberByVerientViewStart------------------
function getDNNumberByVerientView($vid)
{
    global $company_id;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_dn_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_dn_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);

    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            $m = date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            $y = date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            $ym = date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                $y = date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                $ym = date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $responseData = [
        "status" => "success",
        "message" => "DN Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" => $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------DNNumberByVerientViewEnd------------------




//---------------------CNSerialNumberByVerientStart------------------
function getCNNumberByVerient($vid)
{
    global $company_id;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_cn_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_cn_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);

    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            $m = date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            $y = date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            $ym = date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                $y = date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                $ym = date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $ivVerientupdate = queryUpdate("UPDATE `erp_cn_varient` SET `last_inv_no` = '" . $finalArrySerliz . "' WHERE `id` =" . $iv_varient['data']['id'] . "");
    $responseData = [
        "status" => "success",
        "message" => "Invoice Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" =>  $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------CNSerialNumberByVerientEnd------------------



//---------------------CNNumberByVerientViewStart------------------
function getCNNumberByVerientView($vid)
{
    global $company_id;
    $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_cn_varient` WHERE company_id=$company_id AND id=$vid");
    if ($iv_varient['status'] != 'success') {
        $iv_varient = queryGet("SELECT id,verient_serialized,last_inv_no,seperator,reset_time FROM `erp_cn_varient` WHERE company_id=$company_id AND flag_default=0");
    }
    $last_inv = !empty($iv_varient['data']['last_inv_no']) ? $iv_varient['data']['last_inv_no'] : $iv_varient['data']['verient_serialized'];
    $lastivarry = unserialize($last_inv);
    $separator = $iv_varient['data']['seperator'] ?? '';
    $reset = $iv_varient['data']['reset_time'];
    // return ($lastivarry);

    $finalArry = [];
    foreach ($lastivarry as $key => $data) {

        if ($key == "prefix") {
            $finalArry["prefix"] = $data;
        } elseif ($key == "month") {
            $m = date('m'); // Current Month

            $finalArry["month"] = $m;
        } elseif ($key == "yyyy") {
            $y = date('Y'); // Current Year

            $finalArry["yyyy"] = $y;
        } elseif ($key == "fy") {
            $ym = date('Y-m');
            $fyvarient = queryGet("SELECT year_variant_name FROM `erp_year_variant` WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
            if ($fyvarient['status'] == 'success') {
                $fy = $fyvarient['data']['year_variant_name'];  // Current FY


                $finalArry["fy"] = $fy;
            }
        } elseif ($key == "serial") {
            $serial = '';
            $length = strlen($data);
            if ($reset == 'never') {
                if (!empty($iv_varient['data']['last_inv_no'])) {
                    $id = $data + 1;
                } else {
                    $id = $data;
                }
                $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
            } elseif ($reset == 'yearly') {
                $y = date('Y');  // Current Year
                if (isset($lastivarry["yyyy"])) {
                    $year = $lastivarry["yyyy"];
                    if ($year == $y) {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    } else {
                        $id = 1;
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            } elseif ($reset == 'fyearly') {
                $ym = date('Y-m');
                $fyvarient = queryGet("SELECT year_variant_name FROM erp_year_variant WHERE '" . $ym . "' BETWEEN year_start AND year_end AND company_id=$company_id");
                if ($fyvarient['status'] == 'success') {
                    $fy = $fyvarient['data']['year_variant_name'];  // Current FY            
                    if (isset($lastivarry["fy"])) {
                        $fyear = $lastivarry["fy"];
                        if ($fyear == $fy) {
                            if (!empty($iv_varient['data']['last_inv_no'])) {
                                $id = $data + 1;
                            } else {
                                $id = $data;
                            }
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        } else {
                            $id = 1;
                            $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                        }
                    } else {
                        if (!empty($iv_varient['data']['last_inv_no'])) {
                            $id = $data + 1;
                        } else {
                            $id = $data;
                        }
                        $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                    }
                } else {
                    if (!empty($iv_varient['data']['last_inv_no'])) {
                        $id = $data + 1;
                    } else {
                        $id = $data;
                    }
                    $serial = str_pad($id, $length, 0, STR_PAD_LEFT);
                }
            }
            $finalArry["serial"] = $serial;
        }
    }
    // return $invoice_no = 'INV' . str_pad('000012', 2, 0, STR_PAD_LEFT);
    $final_iv = implode($separator, $finalArry);
    $finalArrySerliz = serialize($finalArry);
    $responseData = [
        "status" => "success",
        "message" => "Invoice Number Generated",
        "iv_number_array" => $finalArrySerliz,
        "iv_number" => $final_iv,
        "finalArry" => $finalArry,
        "id" => $iv_varient['data']['id'],
        "iv_number_example" => $final_iv
    ];
    return $responseData;
}
//---------------------CNNumberByVerientViewEnd------------------



//---------------------PoSerialNumberStart------------------
function getPoSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'PO' . $y . $m;
    $old_prefix = substr($lastsl, 0, 6);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 6);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'PO' . $y . $m . str_pad($id, 3, 0, STR_PAD_LEFT);
}
//---------------------PoSerialNumberEnd------------------


//---------------------PRSerialNumberStart------------------
function getPRSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'PR' . $y . $m;
    $old_prefix = substr($lastsl, 0, 6);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 6);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'PR' . $y . $m . str_pad($id, 3, 0, STR_PAD_LEFT);
}
//---------------------PRSerialNumberEnd------------------


//---------------------getGRNSerialNumberStart------------------
function getGRNSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'GRN' . $y . $m;
    $old_prefix = substr($lastsl, 0, 7);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 7);
    } else {
        $sl = 0;
    }
    $sl;
    $id = $sl + 1;
    return $invoice_no = 'GRN' . $y . $m . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------getGRNSerialNumberEnd------------------


//---------------------getGRNIVSerialNumberStart------------------
function getGRNIVSerialNumber($lastsl)
{
    return $invoice_no = str_replace('GRN', 'GRNIV', $lastsl);
}
//---------------------getGRNIVSerialNumberEnd------------------


//---------------------getSRNSerialNumberStart------------------
function getSRNSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'SRN' . $y . $m;
    $old_prefix = substr($lastsl, 0, 7);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 7);
    } else {
        $sl = 0;
    }
    $sl;
    $id = $sl + 1;
    return $invoice_no = 'SRN' . $y . $m . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------getSRNSerialNumberEnd------------------


//---------------------getSRNIVSerialNumberStart------------------
function getSRNIVSerialNumber($lastsl)
{
    return $invoice_no = str_replace('SRN', 'SRNIV', $lastsl);
}
//---------------------getSRNIVSerialNumberEnd------------------

//---------------------SLSerialNumberStart------------------
function getSLSerialNumber($lastsl)
{
    $now_prefix = 'SL';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'SL' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------SLSerialNumberEnd----------------------

//---------------------WHSerialNumberStart------------------
function getWHSerialNumber($lastsl)
{
    $now_prefix = 'WH';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'WH' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------WHSerialNumberEnd----------------------

//---------------------BINSerialNumberStart------------------
function getBINSerialNumber($lastsl)
{
    $now_prefix = 'BIN';
    $old_prefix = substr($lastsl, 0, 3);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'BIN' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------BINSerialNumberEnd----------------------

//---------------------KAMSerialNumberStart------------------
function getKAMSerialNumber($lastsl)
{
    $now_prefix = 'KAM';
    $old_prefix = substr($lastsl, 0, 3);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'KAM' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------KAMSerialNumberEnd----------------------

//---------------------RFQSerialNumberStart------------------
function getRFQSerialNumber($PRSerialNumber, $lastsl = null)
{
    if (!empty($lastsl)) {
        $PRarray = explode('/', $lastsl);
        $saffix = $PRarray[1] + 1;
        $return = $PRarray[0] . '/' . $saffix;
    } else {
        if (!empty($PRSerialNumber)) {
            $return = $PRSerialNumber . '/1';
        } else {
            $return = 'PR' . rand(000, 9999) . rand(000, 9999) . '/1';
        }
    }
    return $return;
}
//---------------------RFQSerialNumberEnd------------------

//---------------------SODelevarySerialNumberStart------------------
function getSODelevarySerialNumber($SOSerialNumber, $lastsl = null)
{
    if (!empty($lastsl)) {
        $PRarray = explode('/', $lastsl);
        $saffix = $PRarray[1] + 1;
        $return = $PRarray[0] . '/' . $saffix;
    } else {
        if (!empty($SOSerialNumber)) {
            $return = $SOSerialNumber . '/1';
        } else {
            $return = 'SO' . rand(000, 9999) . rand(000, 9999) . '/1';
        }
    }
    return $return;
}
//---------------------SODelevarySerialNumberEnd------------------

//---------------------SODelevaryPGISerialNumberStart------------------
function getSODelevaryPGISerialNumber($SOSerialNumber, $lastsl = null)
{
    if (!empty($lastsl)) {
        $PRarray = explode('/', $lastsl);
        $PRarray[2] = $PRarray[2] ?? 0;
        $saffix = $PRarray[2] + 1;
        $return = $PRarray[0] . '/' . $PRarray[1] . '/' . $saffix;
    } else {
        if (!empty($SOSerialNumber)) {
            $return = $SOSerialNumber . '/1';
        } else {
            $return = 'SO' . rand(000, 9999) . rand(000, 9999) . '/1/1';
        }
    }
    return $return;
}
//---------------------SODelevaryPGISerialNumberEnd------------------

//---------------------CostCenterSerialNumberStart------------------
function getCostCenterSerialNumber($lastsl)
{
    $now_prefix = 'C3';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'C3' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------CostCenterNumberEnd------------------

//---------------------ProfitCenterSerialNumberStart------------------
function getProfitCenterSerialNumber($lastsl)
{
    $now_prefix = 'P2';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 4);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'P2' . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//---------------------ProfitCenterNumberEnd------------------
//---------------------CustomerSerialNumberStart------------------
function getCustomerSerialNumber($lastsl)
{
    $com_len = 3;
    $y = date('y');
    $now_prefix = '5' . $y;

    $old_prefix = substr($lastsl, 0, $com_len);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '5' . $y . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------CustomerNumberEnd------------------


//---------------------VendorSerialNumberStart------------------
function getVendorSerialNumber($lastsl)
{
    $com_len = 3;
    $y = date('y');
    $now_prefix = '6' . $y;

    $old_prefix = substr($lastsl, 0, $com_len);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '6' . $y . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------VendorNumberEnd------------------


//---------------------CompanySerialNumberStart------------------
function getCompanySerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = '9' . $y . $m;
    $old_prefix = substr($lastsl, 0, 5);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '9' . $y . $m . str_pad($id, 2, 0, STR_PAD_LEFT);
}
//---------------------CompanyNumberEnd------------------


//---------------------BranchSerialNumberStart------------------
function getBranchSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = '8' . $y . $m;
    $old_prefix = substr($lastsl, 0, 5);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '8' . $y . $m . str_pad($id, 2, 0, STR_PAD_LEFT);
}
//---------------------BranchNumberEnd------------------



//---------------------LocationSerialNumberStart------------------
function getLocationSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = '7' . $y . $m;
    $old_prefix = substr($lastsl, 0, 5);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '7' . $y . $m . str_pad($id, 2, 0, STR_PAD_LEFT);
}
//---------------------LocationNumberEnd------------------
//---------------------getPayrollSerialNumber------------------
function getPayrollSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'PAYRL' . $y . $m;
    $old_prefix = substr($lastsl, 0, 9);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 9);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'PAYRL' . $y . $m . str_pad($id, 2, 0, STR_PAD_LEFT);
}
//---------------------getPayrollSerialNumberEnd------------------


//---------------------getdepreciationSerialNumber------------------
function getDepreciationSerialNumber($lastsl)
{
    $m = date('m');
    $y = date('y');
    $now_prefix = 'DTN' . $y . $m;
    $old_prefix = substr($lastsl, 0, 7);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, 7);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = 'DTN' . $y . $m . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------getdepreciationSerialNumberEnd------------------


//---------------------JournalSerialNumberStart------------------
function getJernalNewCode($lastsl)
{
    $now_prefix = '99';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -8);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '99' . str_pad($id, 8, 0, STR_PAD_LEFT);
}
//---------------------JournalNumberEnd------------------

//---------------------LicenceSerialNumberStart------------------
function getLicenceNewCode($lastsl)
{
    $now_prefix = '15';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -5);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '15' . str_pad($id, 5, 0, STR_PAD_LEFT);
}
//---------------------LicenceNumberEnd------------------

//---------------------RechargeSerialNumberStart------------------
function getRechargeNewCode($lastsl)
{
    $now_prefix = '17';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -9);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '17' . str_pad($id, 9, 0, STR_PAD_LEFT);
}
//---------------------RechargeNumberEnd------------------

//---------------------getDebitNoteNewCodeStart------------------
function getDebitNoteNewCode($lastsl)
{
    $now_prefix = '99';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -8);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '99' . str_pad($id, 8, 0, STR_PAD_LEFT);
}
//---------------------getDebitNoteNewCodeEnd------------------

//---------------------getCreditNoteNewCodeStart------------------
function getCreditNoteNewCode($lastsl)
{
    $now_prefix = '99';
    $old_prefix = substr($lastsl, 0, 2);

    if ($now_prefix == $old_prefix) {
        $sl = substr($lastsl, -8);
    } else {
        $sl = 0;
    }
    $id = $sl + 1;
    return $invoice_no = '99' . str_pad($id, 8, 0, STR_PAD_LEFT);
}
//---------------------getCreditNoteNewCodeEnd------------------

// fetch status master details 
function fetchStatusMasterByCode($code)
{
    $returnData = [];
    global $dbCon;
    global $company_id;

    $ins = "SELECT * FROM `" . ERP_STATUS_MASTER . "` WHERE company_id='$company_id' AND code='$code'";

    $res = queryGet($ins);
    if ($res['numRows'] > 0) {
        $returnData['success'] = "success";
        $returnData['message'] = "Data found!";
        $returnData['data'] = $res['data'];
        $returnData['sql'] = $ins;
    } else {
        $ins2 = "SELECT * FROM `" . ERP_STATUS_MASTER . "` WHERE company_id='0' AND code='$code'";
        $res = queryGet($ins2);
        $returnData = $res;
    }

    return $returnData;
}


function cleanUpString($inputString)
{
    $outputString = $inputString;
    $outputString = trim($inputString, ', ');
    // // Replace consecutive commas with a single comma
    $outputString = preg_replace('/\s+/', ' ', $outputString);
    $outputString = preg_replace('/\s*,\s*/', ',', $outputString);
    $outputString = preg_replace('/,+/', ', ', $outputString);
    // // Replace consecutive spaces with a single space
    $outputString = preg_replace('/\s+/', ' ', $outputString);
    // // Trim leading and trailing spaces
    $outputString = trim($outputString, ', ');

    return $outputString;
}


//Mailer methodsToExec  ------------Start --------------------------------
//$partyCode(Vendor or Customer Code)|| $operationSlug(Name of the operation)||$documentId(Table primary key)|| $documentCode(Code)
function SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null, $partyCode = null, $operationSlug = null, $documentId = null, $documentCode = null)
{
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    global $isEmailActive;

    $company_admin_details_sql = queryGet("SELECT * FROM `tbl_company_admin_details` WHERE fldAdminCompanyId=$company_id")['data'];
    $admin_email = $company_admin_details_sql['fldAdminEmail'];
    $admin_mobile = $company_admin_details_sql['fldAdminPhone'];

    $ins = "INSERT INTO `erp_globalmail` 
                SET
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `partyCode`='$partyCode',
                    `operationSlug`='$operationSlug',
                    `documentId`='$documentId',
                    `documentCode`='$documentCode',
                    `totoaddress`='$to',
                    `mailTitle`='$sub',
                    `msgBody`='$msg',
                    `mailStatus`='1',
                    `created_by`='$created_by',
                    `updated_by`='$updated_by'";
    $globMailIns = queryInsert($ins);
    $globMailId = $globMailIns['insertedId'];

    $newMessage = $msg;
    if ($tmpId == 1 || $tmpId == '') {
        $newMessage     =    '<body style="padding:0; margin:0;">';
        $newMessage     .=    '<div style="width:800px; padding:0; margin:0; font-family:Arial, Helvetica, sans-serif;">';
        $newMessage     .=    '<table width="100%" cellspacing="0" cellpadding="10" border="0">';
        $newMessage     .=    '<tr style="background:#003060;">';
        $newMessage     .=    '<td><img src="' . BASE_URL . 'public/vitwo.png" style="width:110px;"></td>';
        $newMessage     .=    '<td align="right">';
        $newMessage     .=    '<span style="width: 25px; margin-right:5px; display:inline-block;"><img src="' . BASE_URL . 'public/web.png" style="width: 100%;vertical-align: middle;"></span>';
        $newMessage     .=    '<p style="display:inline-block; margin-top: 0; margin-bottom: 0;"><a style=" color: #fff !important;" href="www.vitwo.in">www.vitwo.in</a></p>';
        $newMessage     .=    '</td>';
        $newMessage     .=    '</tr>';
        $newMessage     .=    '<tr align="left" valign="top" style="background: #ecf0f4;">';
        $newMessage     .=    '<td colspan="2" style="color: #333; padding-top: 20px; padding-bottom: 20px; border-top: solid 1px #ccc; border-bottom: solid 1px #ccc;">';
        $newMessage     .=    '<div style="min-height:300px;">';
        $newMessage     .=    '<p>';
        $newMessage     .=    '<img src="' . BASE_URL . 'public/mailstatus/mail-status.php?mail_id=' . $globMailId . '&mailstatus=2" style="height:1px; width:1px">';
        $newMessage     .=    $msg;
        $newMessage     .=    '</p>';
        $newMessage     .=    '<p>';
        $newMessage     .=    'We would love to answer any questions you might have about us, so please check out the FAQ section in our website, or contact us on ' . $admin_email . ' , or call us on our number -  <span style="color:red;">' . $admin_mobile . '</span>';
        $newMessage     .=    '</p>';
        $newMessage     .=    '<p>';
        $newMessage     .=    'Thank you ! <br> ViTWO.ai';
        $newMessage     .=    '</p>';
        $newMessage     .=    '<div>';
        $newMessage     .=    '</div>';
        $newMessage     .=    '</div>';
        $newMessage     .=    '</td>';
        $newMessage     .=    '</tr>';
        $newMessage     .=    '<tr style="background:#003060;">';
        $newMessage     .=    '<td colspan="2">';
        $newMessage     .=    '</td>';
        $newMessage     .=    '</tr>';
        $newMessage     .=    '</table>';
        $newMessage     .=    '</div>';
        $newMessage     .=    '</body>';
    }
    if ($isEmailActive == 'no') {
        return true;
    } else {
        return sendMySMTPmail($to, $sub, $newMessage);
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use function PHPSTORM_META\type;

require_once __DIR__ . '/../../phpmailer/src/Exception.php';
require_once __DIR__ . '/../../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../phpmailer/src/SMTP.php';

// passing true in constructor enables exceptions in PHPMailer
$mail = new PHPMailer(true);


function sendMySMTPmail($to, $sub, $msg)
{
    global $mail;
    try {
        // Server settings

        // $mail->SMTPDebug = 2; 
        // $mail->Debugoutput = 'html'; 

        $mail->isSMTP();
        $mail->Host = 'email-smtp.ap-southeast-1.amazonaws.com';
        $mail->SMTPAuth = true;
        // $mail->SingleTo = true;
        $mail->CharSet = "UTF-8";

        // If post 465 is not working then use this below 25
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // $mail->SMTPSecure = "tls";
        // $mail->Port = 465;


        $mail->Username = "AKIAUJ5K6KLP67SSQSLS";
        $mail->Password = "BH7ajHtI1MyZ80y7wkqL4OqbidJJ2Jo7fDAJTF2tDJLd";
        // AKIAUJ5K6KLPYKRKP57Q
        // HMzxMUnWyvpm8DReGVggZEOlYVCZpioygMF4kZ95
        // YOUR gmail password

        // Sender and recipient settings
        $mail->setFrom('no-reply@vitwo.ai', 'ViTWO AI');
        $mail->addAddress($to);
        $mail->addReplyTo('no-reply@vitwo.ai', 'ViTWO AI'); // to set the reply to

        // Setting the email content
        $mail->IsHTML(true);
        $mail->Subject = $sub;
        $mail->Body = $msg;
        $mail->AltBody = strip_tags($msg);



        if ($mail->send()) {
            $mail->ClearAddresses();
            return true;
        } else {
            $mail->ClearAddresses();
            return false;
        }
        $mail->ClearAddresses();
    } catch (Exception $e) {
        return $e;
    }
}


function sendwhatsappMsg($jsonstring)
{
    if (!empty($jsonstring)) {
        // Assemble the API request URL
        $request_url = "https://graph.facebook.com/v17.0/114805841718624/messages";
        $ch = curl_init($request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonstring); // Set the raw data as the POST body
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', // Set the content type header
            'Authorization: Bearer EAACSBcm738UBO9tKWfVz00SRx71mwLNlGZC3jUpTQOEgkqJZAZCztXhNdo8tRY076a8ShLLqP1Pudsoz800nKfKaAGGZC9AgCEsJxeYJjg7oT0Sc0kZBVC9AJg3ObFyp48PqQtGvPuNN580sdylOXQPPy4H93PxAWjyViTWnQiD38Vc52snO1wtOLSYaqiE6D', // Optional: If your API requires authentication
        ));
        $http_response = curl_exec($ch);
        curl_close($ch);
        return json_encode($http_response, true);
    } else {
        return json_encode([
            "status" => "warning",
            "message" => "Something went wrong, please try again!",
            "data" => ""
        ], true);
    }
}

function fetchStateNameByGstin($gstin)
{
    $gstin = substr($gstin, 0, 2);
    return queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $gstin)["data"]["gstStateName"] ?? "";
}

function fetchStateName()
{
    global $companyCountry;
    return queryGet("SELECT * FROM `erp_gst_state_code` WHERE `country_id`=$companyCountry", true);
}

function fetchInvoiceType()
{
    return queryGet("SELECT * FROM `" . ERP_INVOICE_TYPE . "` ORDER BY id DESC", true);
}

function fetchInvoiceTypeWithgst()
{
    return queryGet("SELECT * FROM `" . ERP_INVOICE_TYPE . "` WHERE gstType IN('yes','both') ORDER BY id DESC", true);
}

function fetchInvoiceTypeWithoutgst()
{
    return queryGet("SELECT * FROM `" . ERP_INVOICE_TYPE . "` WHERE gstType IN('no','both') ORDER BY id DESC", true);
}

function addOpeningBalanceForGlSubGl($data = [])
{
    // global $company_id;
    // global $branch_id;
    // global $location_id;
    // global $created_by;
    // global $updated_by;
    // global $compOpeningDate;
    // if (empty($branch_id)) {
    //     $branch_id = 0;
    // }
    // if (empty($location_id)) {
    //     $location_id = 0;
    // }
    // // $date = $data["date"] ?? "";
    // $date = $compOpeningDate??$data["date"];
    // $gl = $data["gl"] ?? 0;
    // $subgl = $data["subgl"] ?? "";
    // $closing_qty = $data["closing_qty"] ?? 0;
    // $closing_val = $data["closing_val"] ?? 0;

    // $sql = 'INSERT INTO `erp_opening_closing_balance` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '",`date`="' . $date . '",`gl`=' . $gl . ',`subgl`=' . $subgl . ',`closing_qty`=' . $closing_qty . ',`closing_val`=' . $closing_val;
    // return queryInsert($sql);
}

function checkAndUpdateOpeningBalanceForGlSubGl($data = [])
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $date = $data["date"] ?? "";
    $gl = $data["gl"] ?? 0;
    $subgl = $data["subgl"] ?? "";
    $closing_qty = $data["closing_qty"] ?? 0;
    $closing_val = $data["closing_val"] ?? 0;

    $todayDate = date_create(date("Y-m-01"));
    $postingDate = date_create($date);
    $diffObj = date_diff($todayDate, $postingDate);
    $diffDays = $diffObj->format("%R%a");
    if ($diffDays < 0) {

        $sql = 'SELECT * FROM `erp_opening_closing_balance` WHERE date_format(date, "%Y-%m") = date_format("' . $postingDate . '", "%Y-%m") AND `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `gl`=' . $gl . ' AND `subgl`="' . $subgl . '"';
        $prevRecordsObj = queryGet($sql, true);

        $isError = false;
        if ($prevRecordsObj["numRows"] > 0) {
            //update existing month record
            foreach ($prevRecordsObj["data"] as $oneRow) {
                $closing_qty += $oneRow["closing_qty"];
                $closing_val += $oneRow["closing_val"];
                $updateObj = queryUpdate('UPDATE `erp_opening_closing_balance` SET `updated_by`="' . $updated_by . '", `closing_qty`=' . $closing_qty . ',`closing_val`=' . $closing_val . ' WHERE `id`=' . $oneRow["id"]);
                if ($updateObj["status"] != "success") {
                    $isError = true;
                }
            }
        } else {
            //insert new month record
            $sqlIns = 'INSERT INTO `erp_opening_closing_balance` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '",`date`="' . $date . '",`gl`=' . $gl . ',`subgl`="' . $subgl . '",`closing_qty`=' . $closing_qty . ',`closing_val`=' . $closing_val;
            $insertObj = queryInsert($sqlIns);
            if ($insertObj["status"] != "success") {
                $isError = true;
            }
        }

        //update next months record
        $isError2 = false;
        if (!$isError) {
            $prevSql = 'SELECT * FROM `erp_opening_closing_balance` WHERE date_format(date, "%Y-%m") > date_format("' . $postingDate . '", "%Y-%m") AND `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `gl`=' . $gl . ' AND `subgl`="' . $subgl . '"';
            $prevObj = queryGet($prevSql, true);
            //update existing month record
            foreach ($prevObj["data"] as $oneRow) {
                $closing_qty += $oneRow["closing_qty"];
                $closing_val += $oneRow["closing_val"];
                $updateObj = queryUpdate('UPDATE `erp_opening_closing_balance` SET `updated_by`="' . $updated_by . '", `closing_qty`=' . $closing_qty . ',`closing_val`=' . $closing_val . ' WHERE `id`=' . $oneRow["id"]);
                if ($updateObj["status"] != "success") {
                    $isError2 = true;
                }
            }
        }

        if (!$isError && !$isError2) {
            return [
                "status" => "success",
                "message" => "Successfully updated closing balance"
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Failed to update closing balance"
            ];
        }
    }
}


//****    -------------------------Pritam start-------------------------------   */
function getTableSettingsCheckbox($tablename, $pageTableName, $adminId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM " . $tablename . " WHERE pageTableName='" . $pageTableName . "' AND createdBy='" . $adminId . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong1";
        $returnData['data'] = [];
    }
    return $returnData;
}

function getHSNCodeByItemId($itemId)
{
    $prevSql = 'SELECT item.itemName,item.hsnCode,hsn.hsnDescription,hsn.taxPercentage	FROM `erp_inventory_items` as item  LEFT JOIN `erp_hsn_code` as hsn ON item.hsnCode=hsn.hsnCode WHERE item.itemId=' . $itemId;
    return $prevObj = queryGet($prevSql);
}

// function decimalQuantityPreview($number)
// {
//     global $decimalQuantity;
//     $number = $number ?? 0;
//     return number_format($number, $decimalQuantity);
// }
function decimalQuantityPreview($number)
{
    global $decimalQuantity;
    $factor = pow(10, $decimalQuantity);
    return number_format(floor($number * $factor) / $factor, $decimalQuantity, '.', ',');
}
// function decimalValuePreview($amount)
// {
//     global $decimalValue;
//     $amount = $amount ?? 0;
//     return number_format($amount, $decimalValue);
// }
function decimalValuePreview($amount)
{
    global $decimalValue;
    if (!is_numeric($amount)) {
        return 0; // or maybe return '', depending on your needs
    }
    $factor = pow(10, $decimalValue);
    return number_format(floor($amount * $factor) / $factor, $decimalValue, '.', ',');
}
function inputValue($amount)
{
    global $decimalValue;
    $factor = pow(10, $decimalValue);
    return number_format(floor($amount * $factor) / $factor, $decimalValue, '.', '');
}

function decimalValue($amount)
{
    global $decimalValue;
    $amount = $amount ?? 0;
    return round($amount, $decimalValue);
}

function decimalQuantity($number)
{
    global $decimalQuantity;
    $number = $number ?? 0;
    return round($number, $decimalQuantity);
}
function inputQuantity($number)
{
    global $decimalQuantity;
    $factor = pow(10, $decimalQuantity);
    return number_format(floor($number * $factor) / $factor, $decimalQuantity, '.', '');
}

function helperAmount($amt)
{
    global $decimalValue;
    $base = $decimalValue ?? 2;
    $returnValue = floatval($amt);

    $tempVal = explode(".", strval($returnValue));
    $leftVal = $tempVal[0];
    $rightVal = isset($tempVal[1]) ? substr($tempVal[1], 0, $base) : "00";
    return number_format(floatval("$leftVal.$rightVal"), $base, '.', '');
}

function helperQuantity($qty)
{
    global $decimalQuantity;
    $base = $decimalQuantity ?? 3;
    $returnValue = floatval($qty);

    $tempVal = explode(".", strval($returnValue));
    $leftVal = $tempVal[0];
    $rightVal = isset($tempVal[1]) ? substr($tempVal[1], 0, $base) : "000";
    return number_format(floatval("$leftVal.$rightVal"), $base, '.', '');
}

//****    ------------------------Pritam End--------------------------------   */

// ****  -------------------------- Hrithik start ------------------          *****//
function getStateDetail($stateCode)
{
    global $companyCountry;
    $query = queryGet("SELECT * FROM `erp_gst_state_code` WHERE gstStateCode=$stateCode AND country_id=$companyCountry");
    $res = [];
    if ($query['numRows'] > 0) {

        $res = [
            "status" => true,
            "msg" => "success",
            "data" => $query['data']
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "warning state not found",
            "sql" => $query['sql']
        ];
    }
    return $res;
}

function hsnInProperFormat($str)
{
    $end = strpos($str, "-");
    if ($end != null) {
        return substr($str, 0, $end);
    }
    return $str;
}

function getCustomerPrimaryAddressById($id)
{
    $sql = "SELECT * FROM `erp_customer_address` WHERE customer_id=$id AND `customer_address_primary_flag`=1";
    $queryRes = queryGet($sql);
    if ($queryRes['numRows'] > 0) {
        $data = $queryRes['data'];
        $res = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];
        return $res;
    }
    return "";
}

function getVendorBuisnessAddress($id)
{
    $table = 'erp_vendor_bussiness_places';
    $columns = [
        'vendor_business_building_no',
        'vendor_business_flat_no',
        'vendor_business_street_name',
        'vendor_business_pin_code',
        'vendor_business_location',
        'vendor_business_district',
        'vendor_business_state'
    ];
    $whereCondition = "vendor_id=$id";

    $sql = "SELECT " . implode(", ", $columns) . " FROM `$table` WHERE $whereCondition LIMIT 1";
    $queryRes = queryGet($sql);

    if ($queryRes['numRows'] > 0) {
        $data = $queryRes['data'];

        $addressParts = array_filter(array_map('trim', $data), function ($value) {
            return $value !== null && $value !== '';
        });

        return implode(', ', $addressParts);
    }
    return "";
}

// this function is used fetch from item tax rule
function getItemTaxRule($countryId, $sourceCode = "", $destCode = "")
{
    $sql = "SELECT * FROM `erp_tax_rulebook` WHERE `country_id` = $countryId";
    $dbObj = new Database();
    $queryRes = $dbObj->queryGet($sql, true);

    if ($queryRes['numRows'] > 0) {
        $data = $queryRes['data'];
        $cond = ($sourceCode === $destCode) ? "s=d" : "s!=d";

        if ($queryRes['numRows'] == 1) {
            return ["status" => "success", "data" => $data[0]['tax_spit_ratio']];
        }

        foreach ($data as $value) {
            if ($value['tax_condition'] == $cond) {
                return ["status" => "success", "data" => $value['tax_spit_ratio']];
            }
        }
    }
}

// this function is used fetch dynamic lebel 
function getLebels($countryId)
{
    $sql = "SELECT `components` FROM `erp_country_labels` WHERE `country_id` = $countryId";
    $dbObj = new Database();
    $queryRes = $dbObj->queryGet($sql);
    if ($queryRes['numRows'] > 0) {
        return ["status" => "success", "data" => $queryRes['data']['components'], "sql" => $queryRes['query']];
    } else {
        return ["status" => "error", "sql" => $queryRes['query']];
    }
}
function getTaxName($countryId)
{
    $sql = "SELECT `tax_name` FROM `erp_tax_rulebook` WHERE `country_id` = $countryId";
    $dbObj = new Database();
    $queryRes = $dbObj->queryGet($sql);
    if ($queryRes['numRows'] > 0) {
        return ["status" => "success", "data" => $queryRes['data']['tax_name']];
    }
}

function swalAlert2($icon = null, $title = null, $text = null, $url = null)
{
    if ($icon != null && $text != null) {
        ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: `<?= $icon ?>`,
                    title: `<?= $title ?>`,
                    html: `<?= $text ?>`,
                }).then(function() {
                    // Reload the page if no URL is provided
                    if (<?= json_encode($url) ?> == null) {
                        location.reload();
                    } else {
                        window.location.href = `<?= $url ?>`;
                    }
                });
            });
        </script>
<?php
    }
}

function stockQtyImpact($item_array, $flag = "success")
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $updated_by;
    $newqty = 0;
    $error = 0;
    $res = [];
    foreach ($item_array as $item) {

        $usedQty = $item['qty'];
        $itemId = $item['itemId'];

        $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`,`acc_remark`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);
        $oldQty = $goodStockSummaryCheckSql['data']['itemTotalQty'];
        $newqty = $oldQty - ($usedQty);

        $flag_remark = $goodStockSummaryCheckSql['data']['acc_remark'];
        $flgArray = json_decode($flag_remark, true);

        $flagId = $item['id'];
        $flagType = $item['type'];

        if ($flag == "success") {
            $upSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $newqty . ',`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId;
        } else if ($flag == "failed") {
            if (!isset($flgArray[$flagType])) {
                $flgArray[$flagType] = [];
            }

            if (!in_array($flagId, $flgArray[$flagType])) {
                $flgArray[$flagType][] = $flagId;
            }

            $updated_flag_remark = addslashes(json_encode($flgArray));
            $upSql = 'UPDATE `erp_inventory_stocks_summary` SET `acc_flag`= "1",`acc_remark`="' . $updated_flag_remark . '",`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId;
        } else if ($flag == "repost") {
            $flagvar = 1;
            if (!empty($flgArray) && isset($flgArray[$flagType])) {
                $key = array_search($flagId, $flgArray[$flagType]);
                if ($key !== false) {
                    unset($flgArray[$flagType][$key]);
                    $flgArray[$flagType] = array_values($flgArray[$flagType]);
                    if (empty($flgArray[$flagType])) {
                        unset($flgArray[$flagType]);
                    }
                } else {
                    $newqty = $oldQty;
                }
            }

            if (empty($flgArray)) {
                $flagvar = 0;
            }

            $updated_flag_remark = addslashes(json_encode($flgArray));
            $upSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $newqty . ',`acc_flag`= "' . $flagvar . '",`acc_remark`="' . $updated_flag_remark . '",`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId;
        }
        $upRes = queryUpdate($upSql);

        if ($upRes['status'] != "success") {
            $error++;
        }
    }
    return $error === 0 ? "success" : "failed";
}

// check item on falied accounted or not
function checkItemImpactById($itemId)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $goodStockSummaryCheckRes = queryGet('SELECT `itemId`, `acc_flag`,`acc_remark` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);

    if ($goodStockSummaryCheckRes['status'] == "success" && $goodStockSummaryCheckRes['numRows'] > 0) {
        $acc_flag = $goodStockSummaryCheckRes['data']['acc_flag'];
        $acc_remark = $goodStockSummaryCheckRes['data']['acc_remark'];
        $flagArray = json_decode($acc_remark);

        $res = [];
        if (empty($flagArray) && $acc_flag == 0) {
            $res = ["status" => "success", "message" => "Item is cleared"];
        } else {
            $typeArray = [
                "inv" => "Invoice",
                "grn" => "GRN",
                "prodin" => "Production In",
                "dn" => "Debit Note"
            ];
            $messageParts = [];

            foreach ($flagArray as $key => $value) {
                if (!empty($value) && isset($typeArray[$key])) {
                    if ($acc_flag == 1) {
                        $messageParts[] = $typeArray[$key] . " failed accounting";
                    } else {
                        $messageParts[] = $typeArray[$key] . " failed";
                    }
                }
            }

            if (!empty($messageParts)) {
                $message = "This item is on " . implode(" and ", $messageParts) . ".";
                $res = ["status" => "warning", "message" => $message];
            }
        }
        return $res;
    } else {
        return ['status' => 'error', 'message' => 'Item Can not Find'];
    }
}

function summeryDirectStockUpdateByItemId($itemId, $givenQty, $opt = "+")
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $updated_by;

    if ($opt !== '+' && $opt !== '-') {
        return ['status' => 'error', 'message' => 'Invalid stock operation. Use "+" or "-"'];
    }

    $goodStockSummaryCheckRes = queryGet('SELECT `itemId`,`itemTotalQty` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);
    $res = [];
    if ($goodStockSummaryCheckRes['status'] == "success" && $goodStockSummaryCheckRes['numRows'] > 0) {

        $oldQty = $goodStockSummaryCheckRes['data']['itemTotalQty'];
        $newQty = ($opt === '+') ? $oldQty + $givenQty : $oldQty - $givenQty;

        if ($newQty < 0) {
            return ['status' => 'error', 'message' => 'Stock quantity cannot be negative'];
        }

        $upSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $newQty . ',`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId;
        $upRes = queryUpdate($upSql);

        if ($upRes['status'] == "success") {

            if ($opt == "+") {
                $res = ['status' => 'success', 'message' => 'Item Stock on Summery Increased Successfully'];
            } else {
                $res = ['status' => 'success', 'message' => 'Item Stock on Summery Decreased Successfully'];
            }
        } else {
            $res = ['status' => 'warning', 'message' => 'Item Stock on Summery Not Updated'];
        }
    } else {
        $res = ['status' => 'error', 'message' => 'Item Can Not Find'];
    }
    return $res;
}
//Formula to calculate  New Moving Weighted Price / Moving Average Price
function calculateNewMwp($itemId, $newQty, $newPrice, $type = null)
{
    global $company_id;
    global $branch_id;
    global $location_id;

    $movingWeightedPrice = 0;

    $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `priceType`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);
    $vClass = $goodStockSummaryCheckSql["data"]["priceType"];
    $vClass = strtolower($vClass);
    if ($type == "GRN") {
        $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0;
    } else {
        $prevTotalQty = itemTotalStockById($itemId) ?? 0;
    }

    $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0;
    $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice;
    $itemNewTotalQty = (float)$prevTotalQty + $newQty;
    $itemNewTotalPrice = (float)$prevTotalPrice + (($newQty * $newPrice));

    if ($itemNewTotalQty != 0) {
        $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty);
    }
    if (($type == "mat" || $type == "dnrev" || $type == "invrev" || $type == "GRN" || $type == "prodinrev") && $vClass == 'v') {
        mwpImpact($itemId, $itemNewTotalQty, $movingWeightedPrice);
    }
    return $movingWeightedPrice;
}

function mwpImpact($itemId, $qty, $mapValue)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $itemCode = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $itemId . "' AND company_id = '" . $company_id . "'  AND `status`!='deleted'")['data']['itemCode'];

    $updSql = queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $qty . ', `movingWeightedPrice`=' . $mapValue . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);

    $inStock = queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $itemId . ',`itemCode`="' . $itemCode . '",`movingAveragePrice`=' . $mapValue . ',`createdBy`="' . $created_by . '"');

    // return [$updSql, $inStock];
}

// fetch current MWP By item Id using summnery table
function fetchCurrentMwp($itemId)
{
    global $company_id;
    global $branch_id;
    global $location_id;

    $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);

    $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0;
    return $prevMovingWeightedPrice;
}

// For reverse MAP Calculation
function calculateReverseMwp($itemId, $oldQty, $oldPrice, $type = null)
{
    global $company_id;
    global $branch_id;
    global $location_id;

    $movingWeightedPrice = 0;

    $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `priceType`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);
    $vClass = $goodStockSummaryCheckSql["data"]["priceType"];
    $vClass = strtolower($vClass);
    $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0;
    // $prevTotalQty = itemTotalStockById($itemId) ?? 0;
    $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0;
    $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice;

    $itemNewTotalQty = (float)$prevTotalQty - $oldQty;
    if ($itemNewTotalQty != 0) {
        $itemNewTotalPrice = (float)$prevTotalPrice - (($oldQty * $oldPrice));
        $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty);
    }

    if (($type == "grnrev" || $type == "cnrev") && $vClass == 'v') {
        mwpImpact($itemId, $itemNewTotalQty, $movingWeightedPrice);
    }
    return $movingWeightedPrice;
}

// Fetch  MAP For a specific date
function fetchMwpByItemIdDateWise($itemId, $evenDate)
{
    global $company_id;
    $logSql = "SELECT COALESCE(( SELECT map.movingAveragePrice AS mwp FROM erp_inventory_stocks_moving_average AS map WHERE map.companyId = $company_id AND map.itemId = $itemId AND map.createdAt < '" . $evenDate . "' ORDER BY map.createdAt DESC LIMIT 1 ), 0) AS mwp";
    $map = queryGet($logSql)['data']['mwp'] ?? 0;
    return $map;
}

// Item valuation by item id
function fetchValuationByItemId($itemId)
{
    global $company_id;
    $sql = "SELECT itemSum.priceType as vclass FROM `erp_inventory_stocks_summary` as itemSum WHERE itemSum.company_id=$company_id AND itemSum.itemId=$itemId;";
    $vClass = queryGet($sql)['data']['vclass'] ?? '-';
    return strtolower($vClass);
}
function itemTotalStockById($itemId)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $itemSql = "SELECT SUM(lg.itemQty) as itemTotalQty FROM `erp_inventory_stocks_log` as lg WHERE lg.companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND lg.itemId=$itemId";
    $itemTotalQty = queryGet($itemSql)['data']['itemTotalQty'] ?? 0;
    return $itemTotalQty;
}
function findItemTypeByItemId($itemId)
{
    global $company_id;

    $sql = "SELECT i.goodsType as itemTypeId FROM `erp_inventory_items` as i WHERE i.itemId= '$itemId' AND i.company_id=$company_id;";
    $res = queryGet($sql);

    if ($res['status'] == "success" && $res['numRows'] == 1) {
        $itemTypeId = $res['data']['itemTypeId'] ?? 0;

        $itemType = "";
        if ($itemTypeId == 1) {
            $itemType = "RM";
        } else if ($itemTypeId == 2) {
            $itemType = "SFG";
        } elseif ($itemTypeId == 3) {
            $itemType = "FG";
        } elseif ($itemTypeId == 9) {
            $itemType = "ASSET";
        } elseif ($itemTypeId == 5) {
            $itemType = "ServiceS";
        } elseif ($itemTypeId == 7) {
            $itemType = "ServiceP";
        }

        return ['status' => "success", 'msg' => "Item Type Found", 'itemTypeId' => $res['data']['goodsType'], 'itemType' => $itemType];
    }else {
        return ['status' => "error", 'msg' => "Item Not Found", 'sql' => $sql];
    }
}


//****    ------------------------Hrithik End--------------------------------   */

//****    ------------------------Other's Start--------------------------------   */


function getState($stateCode)
{
    $getState = queryGet("SELECT * FROM erp_gst_state_code WHERE gstStateCode = $stateCode");
    // $state  = $getState['data']['gstStateName'];
    return $getState;
}
//company address

function getCompanyAddress($company_id, $branch_id, $location_id, $countryCode)
{

    $branchDetailsObj = queryGet("SELECT branch_gstin ,state as location_state FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state_code FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
    $companyData = array_merge($branchDetailsObj, $locationDetailsObj);
    $country_fields = json_decode(getLebels($countryCode)['data']);
    $res = "<p>" . $companyData['location_building_no'] . "</p>" .
        "<p>Flat No." . $companyData['location_flat_no'] . ", " . $companyData['location_street_name'] . ",</p>" .
        "<p>" . $companyData['location'] . ", " . $companyData['location_city'] . ", " . $companyData['location_district'] . " " . $companyData['location_pin_code'] . "</p>" .
        "<p>State Name: " . $companyData['location_state'];

    if ($country_fields->state_code) {
        $res .= " , Code: " . substr($companyData['branch_gstin'], 0, 2);
        "</p>";
    }
    return $res;
}
function getAllCountry()
{
    $getCountryname = queryGet("SELECT * FROM `erp_countries`", true);
    // $state  = $getState['data']['gstStateName'];
    return $getCountryname;
}

//------------------Get File Url From S3 Bucket-------------------------
function getFileUrlS3($path)
{
    require_once __DIR__ . '/../../s3Config.php';

    if (!empty($path)) {
        $s3 = new s3Config();
        $url = $s3->getUploadedFile($path);

        if (!empty($url)) {
            $headers = @get_headers($url);
            if ($headers && strpos($headers[0], '200') !== false) {
                return $url;
            } else {
                return "File not found on S3";
            }
        } else {
            return "Invalid URL generated";
        }
    } else {
        return "File path is empty";
    }
}

// ----------------Upload file in S3 Bucket---------------------------------
function uploadFileS3($file = [], $uploadPath = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)
{
    if (empty($file) || empty($file['tmp_name'])) {
        return [
            "status" => "error",
            "message" => "No file uploaded.",
            "data" => ""
        ];
    }

    if (!empty($uploadPath)) {
        $uploadPath = preg_match('#/uploads(/.*)#i', str_replace('\\', '/', $uploadPath), $m) ? $m[1] : '';
    }

    $validationError = "";

    $fileExtension = strtolower(pathinfo($file["name"] ?? '', PATHINFO_EXTENSION));
    $fileSize = $file["size"] ?? 0;
    $fileNewName = time() . rand(10000, 99999) . "." . $fileExtension;

    if (!in_array($fileExtension, $allowedExtensions)) {
        $validationError = "Invalid file type.";
    } elseif ($maxSize > 0 && $fileSize > $maxSize) {
        $validationError = "File size exceeds the maximum limit.";
    } elseif ($minSize > 0 && $fileSize < $minSize) {
        $validationError = "File size is too small.";
    }

    if ($validationError === "") {
        require_once __DIR__ . '/../../s3Config.php';
        $s3 = new s3Config();
        $uploadResult = $s3->uploadFileInS3($file, $uploadPath);

        if ($uploadResult['status']) {
            $presignedUrl = getFileUrlS3($uploadResult['key']);
            return [
                "status" => "success",
                "message" => "File uploaded to S3",
                "data" => [
                    "key" => $uploadResult['key'],
                    "url" => $presignedUrl,
                    "mail_url" => $uploadResult['url'],
                ]
            ];
        } else {
            return [
                "status" => "error",
                "message" => $uploadResult['message'],
                "data" => ""
            ];
        }
    } else {
        return [
            "status" => "error",
            "message" => $validationError,
            "data" => ""
        ];
    }
}





require_once("func-gl-summary.php");

require_once("func-whatsapp-api.php");
?>