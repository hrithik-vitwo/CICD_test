<?php
function getAllCOA()
{
    global $dbCon;
    global $company_id;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id='$company_id' AND `status`!='deleted' ORDER BY gl_code DESC";
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

function getCOADetails($id)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE id='$id'";
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

function getBankCashAccountDetails($id)
{
    global $dbCon;
    global $company_id;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id=" . $company_id . " id='$id'";
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

// insert data
function insertBankCashAccount($POST)
{
    global $dbCon;
    $returnData = [];
    global $company_id;
    global $created_by;
    global $updated_by;

    $parentGL = $POST['parentGL'];
    $exParentGL = explode("_", $parentGL)[0];
    $accountType = $POST['addAccountType'];
    $paymentType = $POST['paymentType'];
    // if ($accountType == "cash"){
    //     $bankName = $POST['cashAccount'];
    // } else {
    //     $bankName = $POST['bankName'];
    // }
    $addCashAccount = $POST['addCashAccount'];
    $bankName = $POST['bankName'];
    $ifscCode = $POST['ifscCode'];
    $accountNo = $POST['accountNo'];
    $accountHolderName = $POST['accountHolderName'];
    $bankAddress = $POST['bankAddress'];
    $accForLocation='';
    if($_POST['accForLocation']){
    $accForLocation = implode(',',$POST['accForLocation']);
    }


    if ($accountType == "bank") {
        
        $acc_code = $accountNo;
        $opening_balance=$POST['opening_balance']??0;
        $insert = "INSERT INTO `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` 
                    SET 
                        company_id = '" . $company_id . "',
                        parent_gl = '" . $exParentGL . "',
                        accForLocation = '" . $accForLocation . "',
                        opening_balance = '" . $opening_balance . "',
                        type_of_account = '" . $accountType . "',
                        bank_name = '" . $bankName . "',
                        ifsc_code = '" . $ifscCode . "',
                        account_no = '" . $accountNo . "',
                        account_holder_name = '" . $accountHolderName . "',
                        bank_address = '" . $bankAddress . "',
                        acc_code = '" . $acc_code . "',
                        flag = '" . $paymentType . "',
                        status = 'active',
                        created_by = '" . $created_by . "',
                        updated_by = '" . $updated_by . "'
    ";
    } else {
        $acc_code = time() + rand( 30, 86400 * 3 );
        $opening_balance=$POST['opening_balance_c']??0;
        $insert = "INSERT INTO `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` 
                    SET 
                        company_id = '" . $company_id . "',
                        parent_gl = '" . $exParentGL . "',
                        accForLocation = '" . $accForLocation . "',
                        opening_balance = '" . $opening_balance . "',
                        type_of_account = '" . $accountType . "',
                        bank_name = '" . $addCashAccount . "',
                        acc_code = '" . $acc_code . "',
                        flag = '" . $paymentType . "',
                        status = 'active',
                        created_by = '" . $created_by . "',
                        updated_by = '" . $updated_by . "'
        ";
    }
    if (mysqli_query($dbCon, $insert)) {
        $data = [
            "date" => date('Y-m-d'),
            "gl" => $exParentGL,
            "subgl" => $acc_code,
            "closing_qty" => 0,
            "closing_val" => $opening_balance
        ];
        addOpeningBalanceForGlSubGl($data);
        $returnData['status'] = "success";
        $returnData['insert'] = $insert;
        $returnData['message'] = "Account Created Successfully";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong!";
    }
    return $returnData;
}

// update data
function updateBankCashAccount($POST)
{
    global $dbCon;
    $returnData = [];
    global $created_by;
    global $updated_by;

    $cashAccountId = $POST['cashAccountId'];
    $flag = $POST['paymentType'];
    $accountType = $POST['accountType'];
    if ($accountType == "cash") {
        $bankName = $POST['cashAccount'];
    } else {
        $bankName = $POST['bankName'];
    }
    $ifscCode = $POST['ifscCode'];
    $accountNo = $POST['accountNo'];
    $accountHolderName = $POST['accountHolderName'];
    $bankAddress = $POST['bankAddress']; 
    $accForLocation='';
    if($_POST['accForLocation']){
    $accForLocation = implode(',',$POST['accForLocation']);
    }
// console($POST);
    $insert = "UPDATE `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` 
                    SET 
                      
                       
                        accForLocation = '" . $accForLocation . "',
                        bank_name = '" . $bankName . "',
                        ifsc_code = '" . $ifscCode . "',
                        account_no = '" . $accountNo . "',
                        flag = '".$flag."',
                        account_holder_name = '" . $accountHolderName . "',
                        bank_address = '" . $bankAddress . "',
                        created_by = '" . $created_by . "',
                        updated_by = '" . $updated_by . "' WHERE id='" . $cashAccountId . "'
    ";
    if (mysqli_query($dbCon, $insert)) {
        $returnData['status'] = "success";
        $returnData['message'] = "Updated successfully";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong!";
    }
    return $returnData;
}

function deleteBankCashAccount($POST)
{
    global $dbCon;
    $returnData = [];

    $id = $POST['cashAccountId'];

    $delete = "UPDATE `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` 
                    SET 
                        status = 'deleted' WHERE id='" . $id . "'
    ";
    if (mysqli_query($dbCon, $delete)) {
        $returnData['status'] = "success";
        $returnData['message'] = "Deleted successful";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong!";
    }
    return $returnData;
}
