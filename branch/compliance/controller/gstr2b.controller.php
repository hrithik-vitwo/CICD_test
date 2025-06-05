<?php

class ComplianceGSTR2b
{
    private $periodGstr2b = null;
    private $periodStart = null;
    private $periodEnd = null;
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $compOpeningDate;
    private $branch_gstin;
    private $branch_gstin_code;

    private $authGstinPortalObj;
    private $dbObj;

    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $branch_gstin;
        global $compOpeningDate;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->compOpeningDate = $compOpeningDate;
        $this->branch_gstin = $branch_gstin;
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);
        $this->dbObj = new Database();
    }
    function setUpcomingGstr2bFillingDate()
    {
        $dbObj = new Database(true);
        $dbObj->setSuccessMsg("Gstr2b file date saved success");
        $dbObj->setErrorMsg("Gstr2b file date saved failed!");
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

        $getLastGstr2bFileDaySqlObj = $dbObj->queryGet("SELECT gstr2b_return_period FROM `erp_compliance_gstr2b` WHERE `company_id`=" . $this->company_id . " AND `branch_id`=" . $this->branch_id . " ORDER BY `id` DESC limit 1 ");
        if ($getLastGstr2bFileDaySqlObj["status"] == "success") {
            $getLastGstr2bFileDay = $getLastGstr2bFileDaySqlObj['data']['gstr2b_return_period'];
        } else {
            $getLastGstr2bFileDay = $this->compOpeningDate;
        }
        // console($getLastGstr2bFileDay);

        $gst2bFileFreqDateObj = queryGet("SELECT branch_gstin_file_r2b_day,branch_gstin_file_frequency FROM `erp_branches` WHERE company_id= $this->company_id AND branch_id= $this->branch_id");
        $branch_gstin_file_frequency = $gst2bFileFreqDateObj['data']['branch_gstin_file_frequency'];

        $months = [];
        if ($branch_gstin_file_frequency == "monthly") {
            // $start = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime($getLastGstr2bFileDay)));
            $start = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime('01-'.substr($getLastGstr2bFileDay,0,2).'-'.substr($getLastGstr2bFileDay,2))));

            $end = DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
            if (!$start || !$end) {
                return [];
            }

            $start->modify('first day of next month');
            while ($start <= $end) {
                $months[] = [
                    "period" => $start->format('mY'),
                    "date" => $start->format('Y-m-d')
                ];
                $start->modify('first day of next month');
            }
        }
        // console($months);
        foreach ($months as $month) {
            $insertSql = $dbObj->queryInsert("INSERT INTO `erp_compliance_gstr2b` SET `company_id`=$this->company_id,`branch_id`=$this->branch_id,`gstr2b_return_period`='" . $month['period'] . "',`created_at`='" . $month['date'] . "',`created_by`='Auto',`updated_by`='Auto',`status`='active'");
            // console($insertSql);
        }
        return $dbObj->queryFinish();
    }

    function getPulledData($returnPeriod = null)
    {
        $resultObj =  $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr2b_documents` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `return_period` IN ( '$returnPeriod', DATE_FORMAT(DATE_SUB(STR_TO_DATE(CONCAT('01', '$returnPeriod'), '%d%m%Y'), INTERVAL 1 MONTH), '%m%Y') ) AND `status` NOT IN ('reconciled', 'reversal')", true);

        if ($resultObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "success",
                "data" => $resultObj['data'] ?? []
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "warning",
                "data" => [],
                "sql" => $resultObj
            ];
        }
    }
}
