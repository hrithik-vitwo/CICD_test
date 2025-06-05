<?php

class GeneralLedgerSummaryController
{
    protected $company_id; //Integer
    protected $branch_id; //Integer
    protected $location_id; //Integer
    protected $created_by; //String
    protected $updated_by; //String
    protected $tbl_acc_coa; //String
    protected $tbl_opening; //String
    protected $company_opening_date; //Date

    protected $gl_acc_types; //Array
    protected $report_tree_view; //boolean
    protected $report_date_start; //Date
    protected $report_date_end; //Date

    protected $access_lvl_condition = "";

    function __construct($gl_acc_types = [1, 2, 3, 4], $report_for = "location", $report_tree_view = false)
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by, $compOpeningDate;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->company_opening_date = $compOpeningDate;
        $this->tbl_acc_coa = ERP_ACC_CHART_OF_ACCOUNTS;
        $this->tbl_opening = 'erp_opening_closing_balance';

        $this->gl_acc_types = $gl_acc_types;
        $this->report_tree_view = $report_tree_view;
    
        if ($report_for == "location") {
            $this->access_lvl_condition = "`location_id`=" . $this->location_id;
        } elseif ($report_for == "branch") {
            $this->access_lvl_condition = "`branch_id`=" . $this->branch_id;
        } else {
            $this->access_lvl_condition = "`company_id`=" . $this->company_id;
        }
    }


    public function summary($gl=null){
        
    }



    public function setGlAccTypes($accTypes = [1, 2, 3, 4])
    {
        $this->glAccTypes = $accTypes;
    }

    public function setGlId($accId = null)
    {
        $this->glAccId = $accId;
    }

    public function setGlTreeView($treeView = true)
    {
        $this->glTreeView = $treeView;
    }

    private function getGlList()
    {
        $glWithOpening = null;
        if ($this->glAccId != null) {
            $glWithOpening = 'SELECT t1.id, t1.p_id, t1.gl_code, t1.gl_label, t1.status,t1.glStType,t1.typeAcc, IFNULL(t2.opening_val, 0) AS opening_val
            FROM (
                SELECT id, p_id, gl_code, gl_label, status, glStType, typeAcc
                FROM `' . $this->tbl_acc_coa . '`
                WHERE id = ' . $this->glAccId . ' AND status = "active"
            ) t1 LEFT JOIN (
                SELECT gl, SUM(opening_val) AS opening_val
                FROM ' . $this->tbl_opening . '
                WHERE ' . $this->accessLvlCondition . ' AND DATE_FORMAT(date, "%Y-%m") = DATE_FORMAT("' . $this->start_date . '", "%Y-%m")
                GROUP BY gl
            ) t2 ON t1.id = t2.gl ORDER BY gl_code';
        } else {
            $inAccTypes = implode(",", $this->glAccTypes);
            $treeViewCondition = !$this->glTreeView ? ' AND glStType="account"' : '';

            $glWithOpening = 'SELECT t1.id, t1.p_id, t1.gl_code, t1.gl_label, t1.status,t1.glStType,t1.typeAcc, IFNULL(t2.opening_val, 0) AS opening_val
            FROM (
                SELECT id, p_id, gl_code, gl_label, status, glStType, typeAcc
                FROM `' . $this->tbl_acc_coa . '`
                WHERE typeAcc IN (' . $inAccTypes . ')' . $treeViewCondition . ' AND status = "active" 
            ) t1 LEFT JOIN (
                SELECT gl, SUM(opening_val) AS opening_val
                FROM ' . $this->tbl_opening . '
                WHERE ' . $this->accessLvlCondition . ' AND DATE_FORMAT(date, "%Y-%m") = DATE_FORMAT("' . $this->start_date . '", "%Y-%m")
                GROUP BY gl
            ) t2 ON t1.id = t2.gl ORDER BY gl_code';
        }
        return queryGet($glWithOpening, true);
    }


    private function getDebitCredit($start_date = null, $end_date = null)
    {
        $start_date = $start_date == null ? $this->start_date : $start_date;
        $end_date = $end_date == null ? $this->end_date : $end_date;

        return queryGet('SELECT glId, IFNULL(SUM(credit_amount), 0) AS credit_amount, IFNULL(SUM(debit_amount), 0) AS debit_amount FROM ( ( SELECT c.glId, SUM(c.credit_amount) AS credit_amount, NULL AS debit_amount FROM `erp_acc_credit` c LEFT JOIN `erp_acc_journal` j ON c.journal_id = j.id WHERE j.postingDate >= "' . $start_date . '" AND j.postingDate <= "' . $end_date . '" AND j.' . $this->accessLvlCondition . ' GROUP BY c.glId ) UNION ALL ( SELECT d.glId, NULL AS credit_amount, SUM(d.debit_amount) AS debit_amount FROM `erp_acc_debit` d LEFT JOIN `erp_acc_journal` j ON d.journal_id = j.id WHERE j.postingDate >= "' . $start_date . '" AND j.postingDate <= "' . $end_date . '" AND j.' . $this->accessLvlCondition . ' GROUP BY d.glId ) ) AS result GROUP BY glId', true);
    }

    private function findMyDebitCreditAmount($dataArr = [], $gl = null)
    {
        foreach ($dataArr as $item) {
            if ($item["glId"] == $gl) {
                return [
                    "credit_amount" => $item["credit_amount"],
                    "debit_amount" => $item["debit_amount"]
                ];
            }
        }
        return [
            "credit_amount" => 0.00,
            "debit_amount" => 0.00
        ];
    }

    private function calculateSummary(array $children)
    {
        $summary = [
            'debit_amount' => 0,
            'credit_amount' => 0,
            'opening_val' => 0,
            'closing_val' => 0,
        ];

        foreach ($children as $child) {
            $summary['debit_amount'] += $child['debit_amount'];
            $summary['credit_amount'] += $child['credit_amount'];
            $summary['opening_val'] += $child['opening_val'];
            $summary['closing_val'] += $child['closing_val'];

            if (isset($child['children_summary'])) {
                $summary['debit_amount'] += $child['children_summary']['debit_amount'];
                $summary['credit_amount'] += $child['children_summary']['credit_amount'];
                $summary['opening_val'] += $child['children_summary']['opening_val'];
                $summary['closing_val'] += $child['children_summary']['closing_val'];
            }
        }

        return $summary;
    }

    private function buildTree($parentId = 0)
    {
        $tree = [];
        foreach ($this->treeViewInputData["data"] as $element) {
            if ($element['p_id'] == $parentId) {
                $children = $this->buildTree($element['id']);
                if (!empty($children)) {
                    $element['children'] = $children;
                    $element['children_summary'] = $this->calculateSummary($children);
                }
                $tree[] = $element;
            }
        }
        return $tree;
    }
    
    public function getSummary($start_date = null, $end_date = null)
    {
        if ($start_date == null || $end_date == null) {
            if($start_date == null && $end_date == null){
                $this->start_date = date("Y-m-01");
                $this->end_date = date("Y-m-d");
            }else{
                if($start_date==null){
                    $this->start_date = date("Y-m-01");
                    $this->end_date = date("Y-m-d");
                }else{
                    $this->start_date = $start_date;
                    $this->end_date = $start_date;
                }
            }
        }else {
            $this->start_date = $start_date;
            $this->end_date = $end_date;
        }

        $start_date = $this->start_date;
        $end_date = $this->end_date;

        if($this->includeOpeningBalance){
            $startDate1stDay = date("Y-m-01", strtotime($start_date));
        }else{
            $startDate1stDay = $start_date;
        }


        $openingObj = $this->getGlList();
        $debitCreditForOpening = [];
        if (strtotime($this->start_date) > strtotime($startDate1stDay)) {
            $openingEndDate = date("Y-m-d", strtotime('-1 days', strtotime($start_date)));
            $debitCreditForOpening = $this->getDebitCredit($startDate1stDay, $openingEndDate);
            foreach ($openingObj["data"] as $key => $item) {
                $debitCredit = $this->findMyDebitCreditAmount($debitCreditForOpening["data"], $item["id"]);
                $openingObj["data"][$key]["opening_val"] = $item["opening_val"] + $debitCredit["debit_amount"] - $debitCredit["credit_amount"];
            }
        }

        $debitCreditObj = $this->getDebitCredit($start_date, $end_date);
        // console($debitCreditObj);

        $grandTotalOpening = 0;
        $grandTotalDebit = 0;
        $grandTotalCredit = 0;
        $grandTotalClosing = 0;
        foreach ($openingObj["data"] as $key => $item) {
            $debitCredit = $this->findMyDebitCreditAmount($debitCreditObj["data"], $item["id"]);
            $openingObj["data"][$key]["debit_amount"] = $debitCredit["debit_amount"];
            $openingObj["data"][$key]["credit_amount"] = $debitCredit["credit_amount"];
            $openingObj["data"][$key]["closing_val"] = $item["opening_val"] + $debitCredit["debit_amount"] - $debitCredit["credit_amount"];

            $grandTotalOpening += $openingObj["data"][$key]["opening_val"];
            $grandTotalDebit += $openingObj["data"][$key]["debit_amount"];
            $grandTotalCredit += $openingObj["data"][$key]["credit_amount"];
            $grandTotalClosing += $openingObj["data"][$key]["closing_val"];
        }

        if ($this->glTreeView) {
            $this->treeViewInputData = $openingObj;
            $treeObj = $this->buildTree();

            return [
                "status" => "success",
                "message" => "Summary fetched successfully",
                "start_date" => $this->start_date,
                "end_date" => $this->end_date,
                "data" => $treeObj,
                "grandTotal" => [
                    "grandTotalOpening" => $grandTotalOpening,
                    "grandTotalDebit" => $grandTotalDebit,
                    "grandTotalCredit" => $grandTotalCredit,
                    "grandTotalClosing" => $grandTotalClosing
                ]
            ];
        }else{
            return [
                "status" => "success",
                "message" => "Summary fetched successfully",
                "start_date" => $this->start_date,
                "end_date" => $this->end_date,
                "data" => $openingObj["data"],
                "grandTotal" => [
                    "grandTotalOpening" => $grandTotalOpening,
                    "grandTotalDebit" => $grandTotalDebit,
                    "grandTotalCredit" => $grandTotalCredit,
                    "grandTotalClosing" => $grandTotalClosing
                ]
            ];
        }
    }
}

?>