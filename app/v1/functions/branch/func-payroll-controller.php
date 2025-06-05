<?php
require_once("func-journal.php");



function submit_payroll($POST)
{
    global $dbCon;
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];


    $isValidate = validate($POST, [
        "year" => "required",
        "month" => "required",
        "payroll" => "array"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $payroll = $POST['payroll'];
    $year = $POST['year'];
    $month = $POST['month'];

    $insert = queryInsert("INSERT INTO `erp_payroll_main` SET
            `payroll_month` = $month,
            `payroll_year` = $year,
            `location_id`= $location_id,
            `company_id` = $company_id,
            `branch_id` = $branch_id");

    $payroll_main_id = $insert['insertedId'];


    $gross_sum = 0;
    $pfEmp_sum = 0;
    $pfEmplr_sum = 0;
    $pfAdmin_sum = 0;
    $esi_employee_sum = 0;
    $esi_employeer_sum = 0;
    $ptax_sum = 0;
    $tds_sum = 0;

    foreach ($payroll as $pay) {
        $cost_center_id = $pay['cost_center_id'];
        $gross = $pay['gross'];
        $pf_employee = $pay['pf_employee'] ?? '0';
        $pf_employer = $pay['pf_employer'] ?? '0';
        $pf_admin = $pay['pf_admin'] ?? '0';
        $ptax = $pay['ptax'] ?? '0';
        $esi_employee = $pay['esi_employee'] ?? '0';
        $esi_employeer = $pay['esi_employeer'] ?? '0';
        $tds = $pay['tds'] ?? '0';
        $map_cost_center_id = $pay['map_cc'];


        $gross_sum = $gross_sum +  $gross;
        $pfEmp_sum = $pfEmp_sum +  $pf_employee;
        $pfEmplr_sum = $pfEmplr_sum + $pf_employer;
        $pfAdmin_sum = $pfAdmin_sum +  $pf_admin;
        $ptax_sum = $ptax_sum + $ptax;
        $esi_employee_sum =  $esi_employee_sum + $esi_employee;
        $esi_employeer_sum =  $esi_employeer_sum + $esi_employeer;
        $tds_sum =  $tds_sum + $tds;

        $insert_payroll = "INSERT INTO `erp_payroll` SET 
                        `costcenter_id`=$cost_center_id,
                        `payroll_main_id`= $payroll_main_id,
                        `gross`='" . $gross . "',
                        `pf_employee`='" . $pf_employee . "',
                        `pf_employeer`='" . $pf_employer . "',
                        `pf_admin`='" . $pf_admin . "',
                        `ptax`='" . $ptax . "',
                        `esi_employee`='" . $esi_employee . "',
                        `esi_employeer`='" . $esi_employeer . "',
                        `created_by` = '" . $created_by . "',
                        `updated_by` = '" . $created_by . "',
                        `status` = 9,
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `payroll_year`=$year,
                        `payroll_month` = $month,
                        `alpha_costcenter_id` = $map_cost_center_id

                        ";

        $returnData = queryInsert($insert_payroll);
    }
    $update = queryUpdate("UPDATE `erp_payroll_main` SET
            `sum_gross`= $gross_sum,
            `sum_pf_employee`=$pfEmp_sum ,
            `sum_pf_employeer`=$pfEmplr_sum ,
            `sum_pf_admin`=$pfAdmin_sum ,
            `sum_esi_employee` = $esi_employee_sum ,
            `sum_esi_employeer`= $esi_employeer_sum ,
            `sum_ptax`= $ptax_sum 
            WHERE `payroll_main_id` = $payroll_main_id");


    return $returnData;
}

function submit_api($POST)
{

    global $dbCon;
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];
    $key = $POST['apikey'];

    $insert = queryUpdate("UPDATE `erp_branch_otherslocation` SET `emp_api_key`='" . $key . "' WHERE `othersLocation_id`=$location_id");

    if ($insert['status'] == "success") {
        $returnData['status'] = "Success";
        $returnData['message'] = "API Key Inserted";
    } else {
        $returnData['status'] = "Warning";
        $returnData['message'] = "API Key Insertion Unsuccessful";
    }

    return $returnData;
}
function manual_payroll($POST)
{

    global $dbCon;
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "year" => "required",
        "month" => "required",
        "payroll" => "array"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $payroll = $POST['payroll'];
    $year = $POST['year'];
    $month = $POST['month'];

    $cost_center = $POST['costcenter'];
    // console($cost_center);


    $payroll_check = queryGet("SELECT * FROM `erp_payroll_main` WHERE `company_id`=$company_id AND `location_id` = $location_id AND payroll_month=$month AND payroll_year=$year");
    if ($payroll_check['status'] != "success") {
        $payroll_last_sql = queryGet("SELECT * FROM `erp_payroll_main` WHERE `company_id`=$company_id AND `location_id` = $location_id ORDER BY `payroll_main_id` DESC LIMIT 1");
        $lastsl = $payroll_last_sql['data']['payroll_code'];
        $paroll_code = getPayrollSerialNumber($lastsl);


        $total_gross = 0;
        $total_pf_emp = 0;
        $total_pf_emplr = 0;
        $total_pf_admin = 0;
        $total_esi_emp = 0;
        $total_esi_emplr = 0;
        $total_ptax = 0;
        $total_tds = 0;


        $insert_payroll_main = queryInsert("INSERT INTO `erp_payroll_main` SET `payroll_month`=$month,`payroll_year`=$year,`sum_gross`=$total_gross,`sum_pf_employee`=$total_pf_emp,`sum_pf_employeer`=$total_pf_emplr,`sum_pf_admin`=$total_pf_admin,`sum_esi_employee`=$total_esi_emp,`sum_esi_employeer`=$total_esi_emplr,`sum_ptax`=$total_ptax,`sum_tds`=$total_tds,`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`payroll_code` = '" . $paroll_code . "'");
        // console($insert_payroll_main);
        $last_id = $insert_payroll_main['insertedId'];


        foreach ($cost_center as $cc) {
            //   console($cc);
            $id = $cc['costcenter_id'];
            $gross = $cc['gross_amount'];
            if ($gross > 0) {
                $pf_emp = $cc['pf_empamount'];
                $pf_emplr = $cc['pf_emplramount'];
                $pf_admin = $cc['pf_adamount'];
                $esi_emp = $cc['esi_empamount'];
                $esi_emplr = $cc['esi_emplramount'];
                $ptax = $cc['ptaxamount'];
                $tds = $cc['tdsamount'];

                $total_gross += $gross;
                $total_pf_emp += $pf_emp;
                $total_pf_emplr += $pf_emplr;
                $total_pf_admin += $pf_admin;
                $total_ptax += $ptax;
                $total_esi_emp += $esi_emp;
                $total_esi_emplr += $esi_emplr;
                $total_tds += $tds;

                $insert_payroll = queryInsert("INSERT INTO `erp_payroll` SET `payroll_year`=$year , `payroll_month` = $month, `alpha_costcenter_id`= $id, `gross` = $gross, `pf_employee` = $pf_emp,`pf_employeer`=$pf_emplr,`pf_admin`=$pf_admin ,`esi_employee`=$esi_emp,`esi_employeer`=$esi_emplr,`ptax`= $ptax,`tds`=$tds,`created_by`='" . $created_by . "',`updated_by`='" . $created_by . "',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`payroll_main_id`=$last_id,`status`=9");

                // console($insert_payroll);
            }
        }

        if ($insert_payroll['status'] == "success") {

            $update_payroll_main = queryUpdate("UPDATE `erp_payroll_main` SET `payroll_month`=$month,`payroll_year`=$year,`sum_gross`=$total_gross,`sum_pf_employee`=$total_pf_emp,`sum_pf_employeer`=$total_pf_emplr,`sum_pf_admin`=$total_pf_admin,`sum_esi_employee`=$total_esi_emp,`sum_esi_employeer`=$total_esi_emplr,`sum_ptax`=$total_ptax,`sum_tds`=$total_tds,`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id WHERE `payroll_main_id`=$last_id");
            // console($update_payroll_main);
            if ($update_payroll_main['status'] == "success") {

                $returnData['status'] = "success";
                $returnData['message'] = "Payroll Inserted successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Payroll Main Insertion Failed";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Payroll Insertion Failed";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Payroll Already Exists!";
    }


    return $returnData;
}

function payrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $accountingControllerObj = new Accounting();
    $postingData = $_POST;
    $id = $postingData["payroll_main_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_main` WHERE payroll_main_id=$id AND `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id ORDER BY `payroll_year`, `payroll_month`");

    if ($verusql['status'] == 'success' && $verusql['data']['acconting_status'] == 'Pending') {

        // console($postingData);
        $dateString = $postingData["payroll_month"] . '-' . $postingData["payroll_year"];
        $dateObj = DateTime::createFromFormat('m-Y', $dateString);
        $monthYear = $dateObj->format('F Y');

        $postingDate = date("Y-m-d");

        $sum_pf_employee = $postingData["sum_pf_employee"];
        $sum_pf_employeer = $postingData["sum_pf_employeer"] + $postingData["sum_pf_admin"];
        $sum_ptax = $postingData["sum_ptax"];
        $sum_esi_employee = $postingData["sum_esi_employee"];
        $sum_esi_employeer = $postingData["sum_esi_employeer"];
        $sum_tds = $postingData["sum_tds"];
        $sum_gross = $postingData["sum_gross"];

        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $postingData["documentNo"], // Invoice Doc Number

                "documentDate" => date("Y-m-d"), // Invoice number

                "postingDate" => $postingDate, // current date

                "reference" => $postingData["documentNo"], // grn code

                "remarks" => "Payroll Posting for - " . $monthYear,

                "journalEntryReference" => "payroll"

            ],
            "payrollDetails" => [

                "sum_pf_employee" => $sum_pf_employee,
                "sum_pf_employeer" => $sum_pf_employeer,
                "sum_esi_employee" => $sum_esi_employee,
                "sum_esi_employeer" => $sum_esi_employeer,
                "sum_ptax" => $sum_ptax,
                "sum_tds" => $sum_tds,
                "sum_gross" => $sum_gross

            ]

        ];


        $payrollPostingObj = $accountingControllerObj->payrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_main` SET `journal_id`=' . $payrollPostingObj["journalId"] . ', `acconting_status`="Posted" WHERE `payroll_main_id`=' . $id);

            $totalBsAmount = $sum_gross + $sum_pf_employeer + $sum_esi_employeer;
            $totalPLAmount = $sum_pf_employee + $sum_esi_employee + $sum_ptax + $sum_tds;
            $totalSalryAmount = $totalBsAmount - $totalPLAmount;
            $insert_slry_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$id, doc_no='" . $verusql["documentNo"] . "', `payroll_month`=" . $verusql["payroll_month"] . ",`payroll_year`=" . $verusql["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalSalryAmount,`due_amount`=$totalSalryAmount,`pay_type`='salary',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll);

            $totalPFAmount = $sum_pf_employee + $sum_pf_employeer;
            $insert_pf_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$id, doc_no='" . $verusql["documentNo"] . "', `payroll_month`=" . $verusql["payroll_month"] . ",`payroll_year`=" . $verusql["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalPFAmount,`due_amount`=$totalPFAmount, `pay_type`='pf',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");

            $totalESIAmount = $sum_esi_employee + $sum_esi_employeer;
            $insert_esi_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$id, doc_no='" . $verusql["documentNo"] . "', `payroll_month`=" . $verusql["payroll_month"] . ",`payroll_year`=" . $verusql["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalESIAmount,`due_amount`=$totalESIAmount,`pay_type`='esi',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");

            $insert_ptax_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$id, doc_no='" . $verusql["documentNo"] . "', `payroll_month`=" . $verusql["payroll_month"] . ",`payroll_year`=" . $verusql["payroll_year"] . ",`posting_date`=NOW(),`amount`=$sum_ptax,`due_amount`=$sum_ptax,`pay_type`='ptax',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");

            $insert_tds_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$id, doc_no='" . $verusql["documentNo"] . "', `payroll_month`=" . $verusql["payroll_month"] . ",`payroll_year`=" . $verusql["payroll_year"] . ",`posting_date`=NOW(),`amount`=$sum_tds,`due_amount`=$sum_tds,`pay_type`='tds',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");

            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}


function salaryPayrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "process_id" => "required",
        "bank_id" => "required",
        "amount" => "required",
        "posting_date" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $accountingControllerObj = new Accounting();
    $id = $_POST["process_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_processing` WHERE process_id=$id AND `company_id`=$company_id");

    if ($verusql['status'] == 'success' && $verusql['data']['due_amount'] > 0) {
        $verudata = $verusql['data'];
        // console($_POST);
        // exit();


        $due_amount = $verudata["due_amount"];
        $doc_posting_date = $verudata["posting_date"];
        $amount = $_POST["amount"];
        if ($amount > $due_amount) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid amount!";
            return $returnData;
        }
        $postingDate = $_POST["posting_date"];
        $monthYearObj = new DateTime($postingDate); // Assuming $postingDate is a DateTime object
        $monthYear = $monthYearObj->format('F Y'); // Format as "Month Year"

        $bank_id = $_POST["bank_id"];
        $bankSql = queryGet("SELECT parent_gl,acc_code,bank_name FROM `erp_acc_bank_cash_accounts` WHERE id=$bank_id AND `company_id`=$company_id");
        $bankdata = $bankSql['data'];
        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $verudata["doc_no"], // Invoice Doc Number

                "documentDate" => $doc_posting_date,

                "postingDate" => $postingDate, // current date

                "reference" => $verudata["doc_no"], // grn code

                "remarks" => "Salary Posting amount: " . $amount . " for - " . $monthYear,

                "journalEntryReference" => "payroll-salary"

            ],
            "payrollDetails" => [
                "bank_gl" => $bankdata['parent_gl'],
                "bank_code" => $bankdata['acc_code'],
                "bank_name" => $bankdata['bank_name'],
                "amount" => $amount

            ]

        ];
        if ($due_amount - $amount == 0) {
            $process_status = "posted";
        } else {
            $process_status = "pending";
        }

        // return $PostingInputData;
        $payrollPostingObj = $accountingControllerObj->salaryPayrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_processing` SET `due_amount`=due_amount-' . $amount . ', `status`="' . $process_status . '" WHERE `process_id`=' . $id);
            // console($queryObj);

            $insert_slry_peyroll_log = queryInsert("INSERT INTO `erp_payroll_processing_log` SET process_id=$id, bank_id=$bank_id, journal_id=" . $payrollPostingObj["journalId"] . ", `posting_date`='" . $postingDate . "', `amount`=$amount, `created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll_log);

            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}

function pfPayrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "process_id" => "required",
        "bank_id" => "required",
        "amount" => "required",
        "posting_date" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $accountingControllerObj = new Accounting();
    $id = $_POST["process_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_processing` WHERE process_id=$id AND `company_id`=$company_id");

    if ($verusql['status'] == 'success' && $verusql['data']['due_amount'] > 0) {
        $verudata = $verusql['data'];
        // console($_POST);
        // exit();

        $due_amount = $verudata["due_amount"];
        $doc_posting_date = $verudata["posting_date"];
        $amount = $_POST["amount"];
        if ($amount > $due_amount) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid amount!";
            return $returnData;
        }
        $postingDate = $_POST["posting_date"];
        $monthYearObj = new DateTime($postingDate); // Assuming $postingDate is a DateTime object
        $monthYear = $monthYearObj->format('F Y'); // Format as "Month Year"
        $bank_id = $_POST["bank_id"];
        $bankSql = queryGet("SELECT parent_gl,acc_code,bank_name FROM `erp_acc_bank_cash_accounts` WHERE id=$bank_id AND `company_id`=$company_id");
        $bankdata = $bankSql['data'];
        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $verudata["doc_no"], // Invoice Doc Number

                "documentDate" => $doc_posting_date,

                "postingDate" => $postingDate, // current date

                "reference" => $verudata["doc_no"], // grn code

                "remarks" => "PF Posting amount: " . $amount . " for - " . $monthYear,

                "journalEntryReference" => "payroll-pf"

            ],
            "payrollDetails" => [
                "bank_gl" => $bankdata['parent_gl'],
                "bank_code" => $bankdata['acc_code'],
                "bank_name" => $bankdata['bank_name'],
                "amount" => $amount

            ]

        ];
        if ($due_amount - $amount == 0) {
            $process_status = "posted";
        } else {
            $process_status = "pending";
        }

        //console($PostingInputData);
        $payrollPostingObj = $accountingControllerObj->pfPayrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_processing` SET `due_amount`=due_amount-' . $amount . ', `status`="' . $process_status . '" WHERE `process_id`=' . $id);
            // console($queryObj);

            $insert_slry_peyroll_log = queryInsert("INSERT INTO `erp_payroll_processing_log` SET process_id=$id, bank_id=$bank_id, journal_id=" . $payrollPostingObj["journalId"] . ", `posting_date`='" . $postingDate . "', `amount`=$amount, `created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll_log);

            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}


function esiPayrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "process_id" => "required",
        "bank_id" => "required",
        "amount" => "required",
        "posting_date" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $accountingControllerObj = new Accounting();
    $id = $_POST["process_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_processing` WHERE process_id=$id AND `company_id`=$company_id");

    if ($verusql['status'] == 'success' && $verusql['data']['due_amount'] > 0) {
        $verudata = $verusql['data'];
        // console($_POST);
        // exit();

        $due_amount = $verudata["due_amount"];
        $doc_posting_date = $verudata["posting_date"];
        $amount = $_POST["amount"];
        if ($amount > $due_amount) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid amount!";
            return $returnData;
        }
        $postingDate = $_POST["posting_date"];
        $monthYearObj = new DateTime($postingDate); // Assuming $postingDate is a DateTime object
        $monthYear = $monthYearObj->format('F Y'); // Format as "Month Year"
        $bank_id = $_POST["bank_id"];
        $bankSql = queryGet("SELECT parent_gl,acc_code,bank_name FROM `erp_acc_bank_cash_accounts` WHERE id=$bank_id AND `company_id`=$company_id");
        $bankdata = $bankSql['data'];
        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $verudata["doc_no"], // Invoice Doc Number

                "documentDate" => $doc_posting_date,

                "postingDate" => $postingDate, // current date

                "reference" => $verudata["doc_no"], // grn code

                "remarks" => "ESI Posting amount: " . $amount . " for - " . $monthYear,

                "journalEntryReference" => "payroll-esi"

            ],
            "payrollDetails" => [
                "bank_gl" => $bankdata['parent_gl'],
                "bank_code" => $bankdata['acc_code'],
                "bank_name" => $bankdata['bank_name'],
                "amount" => $amount

            ]

        ];
        if ($due_amount - $amount == 0) {
            $process_status = "posted";
        } else {
            $process_status = "pending";
        }

        //console($PostingInputData);
        $payrollPostingObj = $accountingControllerObj->esiPayrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_processing` SET `due_amount`=due_amount-' . $amount . ', `status`="' . $process_status . '" WHERE `process_id`=' . $id);
            // console($queryObj);

            $insert_slry_peyroll_log = queryInsert("INSERT INTO `erp_payroll_processing_log` SET process_id=$id, bank_id=$bank_id, journal_id=" . $payrollPostingObj["journalId"] . ", `posting_date`='" . $postingDate . "', `amount`=$amount, `created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll_log);

            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}


function ptPayrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "process_id" => "required",
        "bank_id" => "required",
        "amount" => "required",
        "posting_date" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $accountingControllerObj = new Accounting();
    $id = $_POST["process_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_processing` WHERE process_id=$id AND `company_id`=$company_id");

    if ($verusql['status'] == 'success' && $verusql['data']['due_amount'] > 0) {
        $verudata = $verusql['data'];
        // console($_POST);
        // exit();

        $due_amount = $verudata["due_amount"];
        $doc_posting_date = $verudata["posting_date"];
        $amount = $_POST["amount"];
        if ($amount > $due_amount) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid amount!";
            return $returnData;
        }
        $postingDate = $_POST["posting_date"];
        $monthYearObj = new DateTime($postingDate); // Assuming $postingDate is a DateTime object
        $monthYear = $monthYearObj->format('F Y'); // Format as "Month Year"
        $bank_id = $_POST["bank_id"];
        $bankSql = queryGet("SELECT parent_gl,acc_code,bank_name FROM `erp_acc_bank_cash_accounts` WHERE id=$bank_id AND `company_id`=$company_id");
        $bankdata = $bankSql['data'];
        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $verudata["doc_no"], // Invoice Doc Number

                "documentDate" => $doc_posting_date,

                "postingDate" => $postingDate, // current date

                "reference" => $verudata["doc_no"], // grn code

                "remarks" => "P-Tax Posting amount: " . $amount . " for - " . $monthYear,

                "journalEntryReference" => "payroll-pt"

            ],
            "payrollDetails" => [
                "bank_gl" => $bankdata['parent_gl'],
                "bank_code" => $bankdata['acc_code'],
                "bank_name" => $bankdata['bank_name'],
                "amount" => $amount

            ]

        ];
        if ($due_amount - $amount == 0) {
            $process_status = "posted";
        } else {
            $process_status = "pending";
        }

        //console($PostingInputData);
        $payrollPostingObj = $accountingControllerObj->ptPayrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_processing` SET `due_amount`=due_amount-' . $amount . ', `status`="' . $process_status . '" WHERE `process_id`=' . $id);
            // console($queryObj);

            $insert_slry_peyroll_log = queryInsert("INSERT INTO `erp_payroll_processing_log` SET process_id=$id, bank_id=$bank_id, journal_id=" . $payrollPostingObj["journalId"] . ", `posting_date`='" . $postingDate . "', `amount`=$amount, `created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll_log);
            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}


function tdsPayrollAccounting($POST)
{
    global $created_by;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];

    $isValidate = validate($POST, [
        "process_id" => "required",
        "bank_id" => "required",
        "amount" => "required",
        "posting_date" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputs";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $accountingControllerObj = new Accounting();
    $id = $_POST["process_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_processing` WHERE process_id=$id AND `company_id`=$company_id");

    if ($verusql['status'] == 'success' && $verusql['data']['due_amount'] > 0) {
        $verudata = $verusql['data'];
        // console($_POST);
        // exit();

        $due_amount = $verudata["due_amount"];
        $doc_posting_date = $verudata["posting_date"];
        $amount = $_POST["amount"];
        if ($amount > $due_amount) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid amount!";
            return $returnData;
        }
        $postingDate = $_POST["posting_date"];
        $monthYearObj = new DateTime($postingDate); // Assuming $postingDate is a DateTime object
        $monthYear = $monthYearObj->format('F Y'); // Format as "Month Year"
        $bank_id = $_POST["bank_id"];
        $bankSql = queryGet("SELECT parent_gl,acc_code,bank_name FROM `erp_acc_bank_cash_accounts` WHERE id=$bank_id AND `company_id`=$company_id");
        $bankdata = $bankSql['data'];
        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $verudata["doc_no"], // Invoice Doc Number

                "documentDate" => $doc_posting_date,

                "postingDate" => $postingDate, // current date

                "reference" => $verudata["doc_no"], // grn code

                "remarks" => "TDS Posting amount: " . $amount . " for - " . $monthYear,

                "journalEntryReference" => "payroll-tds"

            ],
            "payrollDetails" => [
                "bank_gl" => $bankdata['parent_gl'],
                "bank_code" => $bankdata['acc_code'],
                "bank_name" => $bankdata['bank_name'],
                "amount" => $amount

            ]

        ];
        if ($due_amount - $amount == 0) {
            $process_status = "posted";
        } else {
            $process_status = "pending";
        }

        //console($PostingInputData);
        $payrollPostingObj = $accountingControllerObj->tdsPayrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_processing` SET `due_amount`=due_amount-' . $amount . ', `status`="' . $process_status . '" WHERE `process_id`=' . $id);
            // console($queryObj);

            $insert_slry_peyroll_log = queryInsert("INSERT INTO `erp_payroll_processing_log` SET process_id=$id, bank_id=$bank_id, journal_id=" . $payrollPostingObj["journalId"] . ", `posting_date`='" . $postingDate . "', `amount`=$amount, `created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "'");
            // console($insert_slry_peyroll_log);

            return $payrollPostingObj;
        } else {
            // console($payrollPostingObj);
            return $payrollPostingObj;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong!";
        return $returnData;
    }
}
