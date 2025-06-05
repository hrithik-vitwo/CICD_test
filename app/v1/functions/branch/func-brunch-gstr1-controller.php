<?php
class gstr1Compilance
{

    protected $company_id;
    protected $branch_id;
    protected $created_by;


    function __construct()
    {
        global $company_id, $branch_id, $created_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->created_by = $created_by;
    }

    function insertNewGstr1($POST)
    {
        $isValidate = validate($POST, [
            "gstr1ReturnDate" => "required",
            "totalTaxableAmount" => "required",
            "totalGstCgst" => "required",
            "totalCess" => "required",
            "arn" => "required"
        ]);

        if ($isValidate["status"] != "success") {
            return $isValidate;
        }

        //check the arn is empty or not, if not empty return already filed!

        $arnChksql = queryGet("SELECT * FROM `erp_compliance_gstr1` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `id`=" . $POST['gstr1Id']);
        if ($arnChksql["status"] == "success") {
            if ($arnChksql["data"]["gstr1_return_file_arn"] != "") {
                return [
                    "status" => "warning",
                    "message" => "Already filed!"
                ];
            }
        } else {
            return [
                "status" => "error",
                "message" => "Something went worng, try again!",
                "log" => $arnChksql
            ];
        }


        $newGstDataSql = "UPDATE  `erp_compliance_gstr1` SET `gstr1_return_date`='" . $POST['gstr1ReturnDate'] . "', `gstr1_return_total_taxable`=" . $POST['totalTaxableAmount'] . ",`gstr1_return_total_cgst`=" . $POST['totalGstCgst'] . ",`gstr1_return_total_sgst`=" . $POST['totalGstSgst'] . ",`gstr1_return_total_igst`=" . $POST['totalGstIgst'] . ",`gstr1_return_total_cess`=" . $POST['totalCess'] . ",`gstr1_return_file_arn`='" . $POST['arn'] . "',`created_by`='$this->created_by' WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `id`=" . $POST['gstr1Id'];
        $newGstDataObj = queryInsert($newGstDataSql);
        if ($newGstDataObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "Status has been marked as filed!",
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Something went wrong, try again",
            ];
        }
    }


    function setUpcomingGstr1FillingDate()
    {
        $dbObj = new Database(true);
        $dbObj->setSuccessMsg("Gstr1 file date saved success");
        $dbObj->setErrorMsg("Gstr1 file date saved failed!");

        // $gstr1MonthlyArr = [
        //     "January" => "February",
        //     "February" => "March",
        //     "March" => "April",
        //     "April" => "May",
        //     "May" => "June",
        //     "June" => "July",
        //     "July" => "August",
        //     "August" => "September",
        //     "September" => "October",
        //     "October" => "November",
        //     "November" => "December",
        //     "December" => "January"
        // ];
        $gstr1MonthlyArr = [
            "01" => "02",
            "02" => "03",
            "03" => "04",
            "04" => "05",
            "05" => "06",
            "06" => "07",
            "07" => "08",
            "08" => "09",
            "09" => "10",
            "10" => "11",
            "11" => "12",
            "12" => "01"
        ];

        $getLastGstr1FileDaySqlObj = $dbObj->queryGet("SELECT gstr1_return_period FROM `erp_compliance_gstr1` WHERE `company_id`=" . $this->company_id . " AND `branch_id`=" . $this->branch_id . " ORDER BY `id` DESC limit 1 ");
        $getLastGstr1FileDay = $getLastGstr1FileDaySqlObj['data']['gstr1_return_period'];
        $gst1FileFreqDateObj = queryGet("SELECT branch_gstin_file_r1_day,branch_gstin_file_frequency FROM `erp_branches` WHERE company_id= $this->company_id AND branch_id= $this->branch_id");
        $branch_gstin_file_frequency = $gst1FileFreqDateObj['data']['branch_gstin_file_frequency'];
        $months = [];

        if ($branch_gstin_file_frequency == "monthly") {

            $start = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime('01-'.substr($getLastGstr1FileDay,0,2).'-'.substr($getLastGstr1FileDay,2))));
            $end = DateTime::createFromFormat('d-m-Y',date('d-m-Y'));
            if (!$start || !$end) {
                return [];
            }
            $start->modify('first day of next month');
            while ($start <= $end) {
                $months[] = [
                    "period"=> $start->format('mY'),
                    "date"=> $start->format('Y-m-d')
                ];
                $start->modify('first day of next month');
            }
        }

        foreach($months as $month){
            $insertSql = $dbObj->queryInsert("INSERT INTO `erp_compliance_gstr1` SET `company_id`=$this->company_id,`branch_id`=$this->branch_id,`gstr1_return_period`='".$month['period']."',`created_at`='".$month['date']."',`created_by`='Auto',`updated_by`='Auto',`gstr1_return_file_status`=0,`status`='active'");
        }

        return $dbObj->queryFinish();
    }
}
