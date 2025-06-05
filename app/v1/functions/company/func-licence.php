<?php
//*************************************/INSERT/******************************************//
function createLicence($POST = [])
{
    global $dbCon;
    global $company_id;
    global $created_by;
    global $updated_by;
    $returnData = [];
    $isValidate = validate($POST, [
        "listItem" => "required|array"
    ], [
        "listItem" => "Choose atlast One Package"
    ]);

    if ($isValidate["status"] == "success") {
        $order_code = time() . rand(11, 99);
        // Get start of today
        $startDate = date("Y-m-d 00:00:00");

        // Get end of today
        $endDate = date("Y-m-d 23:59:59");
        $j=0;

        foreach ($POST['listItem'] as $itemKey => $item) {

            for($i=1; $i<=$item['qty'];$i++) {
                $last_sql = queryGet("SELECT recharge_code FROM `" . ERP_COMPANY_LICENCE_RECHARGE_LOG . "` WHERE 1 ORDER BY `recharge_id` DESC LIMIT 1 ");
                $last1 = $last_sql['data']['recharge_code'] ?? '';
                $recharge_code = getRechargeNewCode($last1);

                $days = $item['packageDuration'] - 1;

                // Add 7 days to the current date
                $recharge_end_date = date("Y-m-d", strtotime($endDate . "+$days days")) . ' 23:59:59';
                $unitPrice = $item['unitPrice'];
                $taxPar = 118;
                $tax_amount = $unitPrice - $unitPrice * 100 / $taxPar;
                $sub_totalPrice = $unitPrice - $tax_amount;



                $insRec = "INSERT INTO `" . ERP_COMPANY_LICENCE_RECHARGE_LOG . "` 
                        SET
                            `company_id`='" . $company_id . "',
                            `order_code`='" . $order_code . "',
                            `recharge_code`='" . $recharge_code . "',
                            `package_variant_id`='" . $item['packageVariantId'] . "',
                            `recharge_type`='" . $item['recharge_type'] . "',
                            `package_name`='" . $item['packageTitle'] . "',
                            `variant_name`='" . $item['variantTitle'] . "',
                            `variant_desc`='" . $item['packageDescription'] . "',
                            `base_amount`='" . $unitPrice . "',
                            `sub_total_amount`='" . $sub_totalPrice . "',
                            `tax_amount`='" . $tax_amount . "',
                            `grand_total_amount`='" . $unitPrice . "',
                            `recharge_date`='" . $startDate . "',
                            `recharge_activate_date`='" . $startDate . "',
                            `duration`='" . $item['packageDuration'] . "',
                            `recharge_end_date`='" . $recharge_end_date . "',
                            `ocr_limit`='" . $item['ocr_limit'] . "',
                            `transaction_limit`='" . $item['transaction_limit'] . "',
                            `recharge_created_by`='" . $created_by . "',
                            `recharge_updated_by`='" . $updated_by . "',
                            `recharge_status`='active'";
                $resrech = queryInsert($insRec);

                if ($resrech['status'] == 'success') {

                    $last_sql = queryGet("SELECT licence_code FROM `" . ERP_COMPANY_LICENCE . "` WHERE 1 ORDER BY `licence_id` DESC LIMIT 1 ");
                    $last1 = $last_sql['data']['licence_code'] ?? '';
                    $licence_code = getLicenceNewCode($last1);
                    $licence_code2 = getLicenceNewCode($licence_code);
                    $recharge_id = $resrech['insertedId'];

                    $insLicencCreat = "INSERT INTO `" . ERP_COMPANY_LICENCE . "` 
                        SET
                            `company_id`='" . $company_id . "',
                            `recharge_id`=$recharge_id,
                            `licence_code`=$licence_code,
                            `pair_code`=$licence_code,
                            `licence_title`='" . $item['packageTitle'] . ' - ' . $item['variantTitle'] . "',
                            `licence_desc`='" . $item['packageDescription'] . "',
                            `licence_amount`='" . $unitPrice . "',
                            `start_date`='" . $startDate . "',
                            `duration`='" . $item['packageDuration'] . "',
                            `enddate`='" . $recharge_end_date . "',
                            `ocr_limit`='" . $item['ocr_limit'] . "',
                            `transaction_limit`='" . $item['transaction_limit'] . "',
                            `licence_created_by`='" . $created_by . "',
                            `licence_updated_by`='" . $updated_by . "',
                            `licence_type`='Creator'";
                    $licencC = queryInsert($insLicencCreat);
                    if ($licencC['status'] != 'success') {
                        $j++;
                    }
                    $insLicencAppro = "INSERT INTO `" . ERP_COMPANY_LICENCE . "` 
                        SET
                            `company_id`='" . $company_id . "',
                            `recharge_id`=$recharge_id,
                            `licence_code`=$licence_code2,
                            `pair_code`=$licence_code,
                            `licence_title`='" . $item['packageTitle'] . ' - ' . $item['variantTitle'] . "',
                            `licence_desc`='" . $item['packageDescription'] . "',
                            `licence_amount`=0,
                            `start_date`='" . $startDate . "',
                            `duration`='" . $item['packageDuration'] . "',
                            `enddate`='" . $recharge_end_date . "',
                            `ocr_limit`=0,
                            `transaction_limit`=0,
                            `licence_created_by`='" . $created_by . "',
                            `licence_updated_by`='" . $updated_by . "',
                            `licence_type`='Approver'";
                    $licencA = queryInsert($insLicencAppro);
                    if ($licencA['status'] != 'success') {
                        $j++;
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Information added failed(0)";
                }
            }
        }
        if ($j==0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Licence successfully added in your account.";
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong(0-$j)!";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

//*************************************/UPDATE/******************************************//
function updateLicence($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminKey" => "required",
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:8",
        "adminRole" => "required",
    ], [
        "adminKey" => "Invalid admin",
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:8 character)",
        "adminRole" => "Select a role",
    ]);

    if ($isValidate["status"] == "success") {

        $adminKey = $POST["adminKey"];
        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $adminRole = $POST["adminRole"];

        $sql = "SELECT * FROM `" . ERP_CREDIT_TERMS . "` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `" . ERP_CREDIT_TERMS . "` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "' WHERE `fldAdminKey`='" . $adminKey . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin modified success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin modified failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin not exist";
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
    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllLicence($fldAdminCompanyId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_CREDIT_TERMS . "` WHERE `status`!='deleted' AND fldAdminCompanyId='" . $fldAdminCompanyId . "'";
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
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

//*************************************/SELECT SINGLE/******************************************//
function getDataDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_CREDIT_TERMS . "` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}
//*************************************************Visit CreditTerms*********************************************** */

function VisitCreditTerms($POST)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminCompanyId`='" . $POST["fldAdminCompanyId"] . "' AND `fldAdminBranchId`='" . $POST["fldAdminBranchId"] . "' AND `fldAdminStatus`='active' ORDER BY fldAdminKey ASC limit 1";
    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["logedBranchAdminInfo"]["adminId"] = $row["fldAdminKey"];
            $_SESSION["logedBranchAdminInfo"]["adminName"] = $row["fldAdminName"];
            $_SESSION["logedBranchAdminInfo"]["adminEmail"] = $row["fldAdminEmail"];
            $_SESSION["logedBranchAdminInfo"]["adminPhone"] = $row["fldAdminPhone"];
            $_SESSION["logedBranchAdminInfo"]["adminRole"] = $row["fldAdminRole"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] = $row["fldAdminCompanyId"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] = $row["fldAdminBranchId"];
            $_SESSION["logedBranchAdminInfo"]["adminType"] = 'branch';
            $returnData["status"] = "success";
            $returnData["message"] = "Login success";
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Invalid Credentials, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Something went wrong, Try again...!";
    }
    return $returnData;
}

//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusCreditTerms($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $tableName = ERP_CREDIT_TERMS;
    $returnData["status"] = null;
    $returnData["message"] = null;
    if (!empty($data)) {
        $id = isset($data["id"]) ? $data["id"] : 0;
        $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
        $prevExeQuery = mysqli_query($dbCon, $prevSql);
        $prevNumRecords = mysqli_num_rows($prevExeQuery);
        if ($prevNumRecords > 0) {
            $prevData = mysqli_fetch_assoc($prevExeQuery);
            $newStatus = "deleted";
            if ($data["changeStatus"] == "active_inactive") {
                $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
            }
            $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
            if (mysqli_query($dbCon, $changeStatusSql)) {
                $returnData["status"] = "success";
                $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
            } else {
                $returnData["status"] = "error";
                $returnData["message"] = "Something went wrong, Try again...!";
            }
            $returnData["changeStatusSql"] = $changeStatusSql;
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Something went wrong, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Please provide all valid data...!";
    }
    return $returnData;
}

//*************************************/END/******************************************//