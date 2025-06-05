<?php

function getMonthsForPrevPosting($postingDate = null)
{
    $postingDate = $postingDate != null ? $postingDate : date("Y-m-d");
    $startDate = new DateTime(date("Y-m-01", strtotime($postingDate)));
    $endDate = new DateTime(date("Y-m-01"));
    $months = [];
    $current = clone $startDate;
    while ($current <= $endDate) {
        $months[] = $current->format('Y-m');
        $current->modify('+1 month');
    }
    return $months;
}

function post_open_close($POST)
{
    // console($POST);
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    $returnData = [];

    $isValidate = validate($POST, [
        "documentDate" => "required",
        "gl" => "required"
    ]);
    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $doc_date = $POST['documentDate'];
    $gl = $POST['gl'];
    $subgl = $POST['subgl'] ?? "";
    $quantity = $_POST['val'] ?? 0;
    $amount = $_POST['amount'] ?? 0;
    $rate = ($_POST['rate'] > 0) ? $_POST['rate'] : 0;
    $sl = ($_POST['sl'] ?? "") !== "" ? $_POST['sl'] : 0;
    $subgl_ex = explode('|', $subgl);
    $subgl_code = $subgl_ex[0] ?? "";

    if (isset($subgl) && !empty($subgl)) {

        $check_subgl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`='" . $company_id . "' AND `location_id`=$location_id AND `branch_id`=$branch_id AND `subgl`='" . $subgl_code . "'");

        if ($check_subgl['numRows'] > 0) {
            $prev_sub_amount = $check_subgl['data']['opening_val'];
            $new_sub_amount = $prev_sub_amount + $amount;
            $up_sub_id = $check_subgl['data']['id'];
            //
            $update_subgl = queryUpdate("UPDATE `erp_opening_closing_balance` SET `updated_by`='" . $updated_by . "',`opening_qty`='" . $quantity . "',`opening_val`=$new_sub_amount ,`rate`= $rate,`storage_location`=$sl WHERE `id`= $up_sub_id");
            console($update_subgl);
            // exit();


            $check_gl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`='" . $company_id . "' AND `location_id`=$location_id AND `branch_id`=$branch_id AND `gl`=$gl AND `subgl`=''");

            if ($check_gl['numRows'] > 0) {
                $gl_id = $check_gl['data']['id'];
                $prev_gl_amount = $check_gl['data']['opening_val'];
                if ($prev_gl_amount > $amount) {
                    $new_gl_amount = $prev_gl_amount - $amount;
                } else {
                    $new_gl_amount = 0;
                }
                //
                $updategl = queryUpdate("UPDATE `erp_opening_closing_balance` SET `updated_by`='" . $updated_by . "',`opening_val`=$new_gl_amount WHERE `id`=$gl_id");
                console($updategl);

                //exit();
            }
        } else {
            //
            $insert_subgl = queryInsert("INSERT INTO `erp_opening_closing_balance` SET `company_id`='" . $company_id . "',`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "',`date`='" . $doc_date . "',`gl`=$gl,`subgl`='" . $subgl_code . "',`opening_qty`='" . $quantity . "',`opening_val`= $amount,`rate`= '" . $rate . "',`storage_location`='" . $sl . "'");
            console($insert_subgl);
            // exit();

            $check_gl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`='" . $company_id . "' AND `location_id`=$location_id AND `branch_id`=$branch_id AND `gl`=$gl AND `subgl`=''");
            if ($check_gl['numRows'] > 0) {
                $gl_id = $check_gl['data']['id'];
                $prev_gl_amount = $check_gl['data']['opening_val'];
                if ($prev_gl_amount > $amount) {
                    $new_gl_amount = $prev_gl_amount - $amount;
                } else {
                    $new_gl_amount = 0;
                }
                //
                $updategl = queryUpdate("UPDATE `erp_opening_closing_balance` SET `updated_by`='" . $updated_by . "',`opening_val`=$new_gl_amount WHERE `id`=$gl_id");
                console($update_subgl);
                // exit();
            }
        }

        $select_item = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemCode`= $subgl_code");
        $item_id = $select_item['data']['itemId'];
        $select_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$item_id");
        $update_qty = $select_summary['data']['itemTotalQty'] + $quantity;
        $update_summary = queryUpdate("UPDATE `erp_inventory_stocks_summary` SET `movingWeightedPrice` = $rate ,`$sl` = $quantity , `itemTotalQty`=  $update_qty WHERE `itemId`=$item_id");
    } else {
        $check_gl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id AND `gl`=$gl AND  `subgl`=''");
        if ($check_gl['numRows'] > 0) {
            $id = $check_gl['data']['id'];
            $prev_gl_amount = $check_gl['data']['opening_val'];
            $new_gl_amount = $amount + $prev_gl_amount;

            $updategl = queryUpdate("UPDATE `erp_opening_closing_balance` SET `updated_by`='" . $updated_by . "',`opening_qty`='" . $quantity . "',`opening_val`=$new_gl_amount WHERE `id` =$id ");
            console($updategl);
            // exit();
        } else {
            //
            $insert_gl = queryInsert("INSERT INTO `erp_opening_closing_balance` SET `company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "',`date`='" . $doc_date . "',`gl`=$gl, `subgl`='', `opening_qty`='" . $quantity . "',`opening_val`=$amount");
            console($insert_gl);
            // exit();
        }
    }

    $openingBalUpdateMonths = getMonthsForPrevPosting($doc_date);
    $openingBalUpdateMonthsNum = count($openingBalUpdateMonths);

    $prevOpeningLog = [];

    if ($openingBalUpdateMonthsNum > 1) {
        foreach ($openingBalUpdateMonths as $monthKey => $oneMonth) {
            if ($monthKey == 0 && $subgl_code == "") {
                continue;
            }

            $prevCheckObj = queryGet('SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $gl . ' AND `subgl`="' . $subgl_code . '"');
            if ($prevCheckObj["status"] == "success") {
                if ($monthKey == 0) {

                    $saveObj = queryUpdate('UPDATE `erp_opening_closing_balance` SET `closing_val`=`closing_val`+' . $amount . ' WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $gl . ' AND `subgl`="' . $subgl_code . '"');

                    $prevOpeningLog[] = ["Obj" => $saveObj, "Log" => $oneMonth . "=>Change the closing balance"];
                } elseif ($monthKey == $openingBalUpdateMonthsNum - 1) {

                    $saveObj = queryUpdate('UPDATE `erp_opening_closing_balance` SET `opening_val`=`opening_val`+' . $amount . ' WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $gl . ' AND `subgl`="' . $subgl_code . '"');

                    $prevOpeningLog[] = ["Obj" => $saveObj, "Log" => $oneMonth . "=>Change the opening balance"];
                } else {

                    $saveObj = queryUpdate('UPDATE `erp_opening_closing_balance` SET `opening_val`=`opening_val`+' . $amount . ', `closing_val`=`closing_val`+' . $amount . ' WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $gl . ' AND `subgl`="' . $subgl_code . '"');

                    $prevOpeningLog[] = ["Obj" => $saveObj, "Log" => $oneMonth . "=>Change the opening and closing balance both"];
                }
            } else {
                $saveObj = queryInsert('INSERT INTO `erp_opening_closing_balance` SET `opening_val`=' . $amount . ', `closing_val`=' . $amount . ', `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $gl . ', `subgl`="' . $subgl_code . '"');
                $prevOpeningLog[] = ["Obj" => $saveObj, "Log" => $oneMonth . "=>Add the opening and closing balance both"];
            }
        }
    }


    $prevOpeningLog = $_POST;


    $returnData["status"] = "success";
    $returnData["message"] = "Success!";
    $returnData["Log"] = $prevOpeningLog;
    $returnData["openingBalUpdateMonths"] = $openingBalUpdateMonths;
    return $returnData;
}
