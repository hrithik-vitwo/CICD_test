<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

class OpeningClosingWrongDataResolver
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $branch_gstin;
    private $companyOpeningDate;
    private $dbObj;
    private $transactionData = [];
    private $openingData = [];
    private $erp_opening_closing_balance_tbl = "erp_opening_closing_balance";
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
        $this->branch_gstin = $branch_gstin;
        $this->dbObj = new Database();
        $this->companyOpeningDate = date("Y-m-d", strtotime($compOpeningDate));
    }

    public function resolve($data)
    {
        // console($data);
        // exit();
        $db = new Database(true);
        $currentMonthLastDate = date("Y-m-t");
        foreach ($data as $subGlCode => $subGlData) {
            foreach ($subGlData as $yyyymmdd => $row) {
                $glId = $row["glId"];
                $openAmount = round($row["openAmount"], 4);
                $logAmount = round($row["logAmount"], 4);
                $computedAmount = round($row["computedAmount"], 4);
                $monthType = $row["monthType"];
                $rowDateMonthFirstDay = date("Y-m-01", strtotime($yyyymmdd));
                if ($monthType === "first") {
                    $sqlInsertColumns = "`opening_val`=$openAmount, `closing_val`=$logAmount,";
                    $sqlUpdateColumns = "`closing_val`=$logAmount,";
                } else if ($monthType === "last") {
                    $sqlInsertColumns = "`opening_val`=$openAmount, `closing_val`=0,"; 
                    $sqlUpdateColumns = "`opening_val`=$openAmount, ";
                } else {
                    $sqlInsertColumns = "`opening_val`=$openAmount, `closing_val`=$logAmount,";
                    $sqlUpdateColumns = "`opening_val`=$openAmount, `closing_val`=$logAmount,";
                }

                $prevObj = $db->queryGet("SELECT * FROM `$this->erp_opening_closing_balance_tbl` WHERE `location_id`=$this->location_id AND `branch_id`=$this->branch_id AND `company_id`=$this->company_id AND `date`='$rowDateMonthFirstDay' AND `gl`=$glId");
              
                if ($prevObj["numRows"] > 0) {
                    $db->queryUpdate("UPDATE `$this->erp_opening_closing_balance_tbl` SET $sqlUpdateColumns `updated_by`='$this->updated_by' WHERE `location_id`=$this->location_id AND `branch_id`=$this->branch_id AND `company_id`=$this->company_id AND `date`='$rowDateMonthFirstDay' AND `gl`=$glId");
                } else {
                    $db->queryInsert("INSERT INTO `$this->erp_opening_closing_balance_tbl` SET $sqlInsertColumns `updated_by`='$this->updated_by', `created_by`='$this->created_by', `location_id`=$this->location_id, `branch_id`=$this->branch_id, `company_id`=$this->company_id, `date`='$rowDateMonthFirstDay',`subgl` = ' ',`gl`=$glId");
                }
                // console([
                //     "yyyymmdd" => $yyyymmdd,
                //     "openAmount" => $openAmount,
                //     "logAmount" => $logAmount,
                //     "computedAmount" => $computedAmount
                // ]);
            }
        }
        return $db->queryFinish();
    }
}


if (isset($_POST["formData"])) {
    // console($_POST);
    // exit();
    $data = json_decode($_POST["formData"], true);
    $wrongDataResolver = new OpeningClosingWrongDataResolver();
    $resultObj = $wrongDataResolver->resolve($data);
    echo json_encode($resultObj, true);
    // swalToast($resultObj["status"], $resultObj["message"]);
    // console($resultObj);
    // exit();
}
