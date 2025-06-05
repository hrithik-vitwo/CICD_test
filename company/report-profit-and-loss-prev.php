<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
// include("../app/v1/functions/company/func-branches.php");


class ProfitAndLossForOneDate
{
    protected $company_id;
    protected $branch_id;
    protected $location_id;
    protected $created_by;
    protected $updated_by;

    protected $balanceSheetTempData = [];
    protected $reportDate = "";

    function __construct($date = null)
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;

        $this->reportDate = $date;

        $erp_acc_coa = ERP_ACC_CHART_OF_ACCOUNTS;
        $sql = 'SELECT BS.gl_code,BS.gl_label,BS.p_id,SUM(amount) AS total_amount FROM ((SELECT coa.gl_code AS gl_code,coa.gl_label AS gl_label,coa.p_id AS p_id,SUM(closing_val) AS amount FROM erp_opening_closing_balance AS closing LEFT JOIN `' . $erp_acc_coa . '` AS coa ON closing.gl=coa.id WHERE closing.company_id=' . $this->company_id . ' AND DATE_FORMAT(DATE_SUB("' . $date . '", INTERVAL 1 MONTH),"%Y-%m")=DATE_FORMAT(closing.date,"%Y-%m") GROUP BY coa.gl_code,coa.gl_label,coa.p_id)

       UNION
       
       (SELECT coa.gl_code AS gl_code,coa.gl_label AS gl_label,coa.p_id AS p_id,SUM(closing_val) AS amount FROM erp_opening_closing_balance AS closing LEFT JOIN `' . $erp_acc_coa . '` AS coa ON closing.gl=coa.id LEFT JOIN (SELECT company_id,opening_date FROM erp_companies WHERE company_id=' . $this->company_id . ') AS company ON closing.company_id=company.company_id WHERE closing.company_id=' . $this->company_id . ' AND DATE_FORMAT(closing.date,"%Y-%m")=DATE_FORMAT("' . $date . '","%Y-%m") AND "' . $date . '" >= company.opening_date AND DATE_FORMAT("' . $date . '","%Y-%m")=DATE_FORMAT(company.opening_date,"%Y-%m") GROUP BY coa.gl_code,coa.gl_label,coa.p_id)
       
       
       UNION
       (SELECT
       gl_code,
           gl_label,
           p_id,
           SUM(amount) AS amount
       FROM
       ((/* Retrieve all credit transactions */
       SELECT
       credit_coa.gl_code AS gl_code,
           credit_coa.gl_label AS gl_label,
           credit_coa.p_id AS p_id,
           credit.credit_amount * (-1) AS amount
       FROM
       erp_acc_journal AS journal
       INNER JOIN
       erp_acc_credit AS credit
         ON
           journal.id = credit.journal_id
       INNER JOIN
       `' . $erp_acc_coa . '` AS credit_coa
           ON
           credit.glId = credit_coa.id
       WHERE
       credit_coa.typeAcc IN (3,4)
           AND journal.postingDate BETWEEN DATE_FORMAT("' . $date . '","%Y-%m-01") AND "' . $date . '"
         AND journal.company_id=' . $this->company_id . ')  
       UNION
       (/* Retrieve all debit transactions */    
       SELECT
       debit_coa.gl_code AS gl_code,
           debit_coa.gl_label AS gl_label,
           debit_coa.p_id AS p_id,
           debit.debit_amount AS amount
       FROM
       erp_acc_journal AS journal
       INNER JOIN
       erp_acc_debit AS debit
         ON
           journal.id = debit.journal_id
       INNER JOIN
       `' . $erp_acc_coa . '` AS debit_coa
           ON
           debit.glId = debit_coa.id
       WHERE
       debit_coa.typeAcc IN (3,4)
           AND journal.postingDate BETWEEN DATE_FORMAT("' . $date . '","%Y-%m-01") AND "' . $date . '"
       AND journal.company_id=' . $this->company_id . ')) AS transaction
       GROUP BY
       gl_code, gl_label,p_id
       ORDER BY
       gl_code)) AS BS
       GROUP BY
       gl_code,gl_label,p_id';

        $this->balanceSheetTempData = queryGet($sql, true)["data"];
    }

    function getBalanceTree($p_id = 0)
    {
        $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$this->company_id AND `p_id`=" . $p_id . "  AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
        $tree = [];
        $groupTotal = 0;
        foreach ($queryObj["data"] as $row) {
            $children = $this->getBalanceTree($row['id']);
            $gl_bl_total_amount = 0;
            if (($key = array_search($row['gl_code'], array_column($this->balanceSheetTempData, 'gl_code'))) > -1) {
                $gl_bl_total_amount = $this->balanceSheetTempData[$key]["total_amount"];
            }
            // $groupTotal+=$gl_bl_total_amount;
            $tree[] = array(
                'id' => $row['id'],
                'gl_label' => $row['gl_label'],
                'gl_code' => $row['gl_code'],
                'glStType' => $row['glStType'],
                'typeAcc' => $row['typeAcc'],
                'gl_total_amount' => $gl_bl_total_amount,
                'data' => $children
            );
        }
        // $tree["groupTotal"] = $groupTotal;
        return $tree;
    }
}




?>
<style>
    td.font-bold.bg-alter {
        background: #afc1d2;
    }

    td.bg-grey.text-white {
        background: #003060;
    }
</style>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?php
    if (isset($_GET["template"])) {
    ?>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">

                        <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Balance Sheet</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>

                        <div class="row">
                            <?php
                            // $balanceSheet = fetchBalanceSheet();
                            // console($balanceSheet);
                            ?>
                        </div>

                        <div class="card card-tabs">
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="overflow: auto;">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Particulars</th>
                                                <th>April 21</th>
                                                <th>May 21</th>
                                                <th>June 21</th>
                                                <th>July 21</th>
                                                <th>Aug 21</th>
                                                <th>Sept 21</th>
                                                <th>Oct 21</th>
                                                <th>Nov 21</th>
                                                <th>Dec 21</th>
                                                <th>Jan 21</th>
                                                <th>Feb 21</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold text-lg">EQUITY AND LIABILITY</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Shareholder's Fund :</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                            </tr>
                                            <tr>
                                                <td>Share Capital</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                            </tr>
                                            <tr>
                                                <td>Reverse and Supplies</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Long Terms Borrowings</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Deferred Tax Liability</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Borrowings</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Trade payables</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Provisions</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td>Other Current Liability</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                            </tr>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold text-lg">ASSETS</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current assets :</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                            </tr>
                                            <tr>
                                                <td>Fixed Assets</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current assets :</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                            </tr>
                                            <tr>
                                                <td>Stock in Hand</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                            </tr>
                                            <tr>
                                                <td>Trade receivables</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                            </tr>
                                            <tr>
                                                <td>Cash and cash equivalents</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                            </tr>
                                            <tr>
                                                <td>Current Investments</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Other Current Assets</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Loans and Advances</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-grey text-white">Net Worth</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    <?php
    } elseif (isset($_GET["mom"])) {
    ?>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">

                        <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Balance Sheet</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>

                        <div class="row">
                            <?php
                            // $balanceSheet = fetchBalanceSheet();
                            // console($balanceSheet);
                            ?>
                        </div>

                        <div class="card card-tabs">
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="overflow: auto;">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Particulars</th>
                                                <th>April 21</th>
                                                <th>May 21</th>
                                                <th>June 21</th>
                                                <th>July 21</th>
                                                <th>Aug 21</th>
                                                <th>Sept 21</th>
                                                <th>Oct 21</th>
                                                <th>Nov 21</th>
                                                <th>Dec 21</th>
                                                <th>Jan 21</th>
                                                <th>Feb 21</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold text-lg">EQUITY AND LIABILITY</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Shareholder's Fund :</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                            </tr>
                                            <tr>
                                                <td>Share Capital</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                                <td>200.00</td>
                                            </tr>
                                            <tr>
                                                <td>Reverse and Supplies</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                                <td>750.05</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Long Terms Borrowings</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Deferred Tax Liability</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                                <td>2.02</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Borrowings</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Trade payables</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Provisions</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td>Other Current Liability</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                            </tr>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold text-lg">ASSETS</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current assets :</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                                <td class="font-bold bg-alter">254.27</td>
                                            </tr>
                                            <tr>
                                                <td>Fixed Assets</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                                <td>71.65</td>
                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                                <td>182.62</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current assets :</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                                <td class="font-bold bg-alter">914.50</td>
                                            </tr>
                                            <tr>
                                                <td>Stock in Hand</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                                <td>9.61</td>
                                            </tr>
                                            <tr>
                                                <td>Trade receivables</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                                <td>802.38</td>
                                            </tr>
                                            <tr>
                                                <td>Cash and cash equivalents</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                                <td>-26.80</td>
                                            </tr>
                                            <tr>
                                                <td>Current Investments</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Other Current Assets</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                                <td>30.19</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Loans and Advances</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                                <td>99.12</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                                <td class="font-bold bg-alter">1,168.77 </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-grey text-white">Net Worth</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                                <td>950.09</td>
                                                <td class="bg-grey text-white">950.09</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    <?php
    } else {
    ?>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">

                        <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Profit And Loss Statement</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>

                        <div class="row">
                            <?php
                            $profitAndLossReportDate = (isset($_GET["filter"]) && $_GET["filter"] != "") ? base64_decode($_GET["filter"]) : date("Y-m-d");

                            $profitAndLossReportDate = (date('Y-m-d', strtotime($profitAndLossReportDate)) == $profitAndLossReportDate) ? $profitAndLossReportDate : date("Y-m-d");

                            $todayDate = date_create(date("Y-m-d"));
                            $filterDate = date_create($profitAndLossReportDate);
                            $diffObj = date_diff($todayDate, $filterDate);
                            $diffDays = $diffObj->format("%R%a");
                            if ($diffDays > 0) {
                                $profitAndLossReportDate = date("Y-m-d");
                            }

                            $profitAndLossObj = new ProfitAndLossForOneDate($profitAndLossReportDate);
                            $balTreeObj = $profitAndLossObj->getBalanceTree(0);
                            // console($balSheetTreeObj);
                            ?>
                        </div>

                        <div class="card card-tabs">
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="overflow: auto;">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Particulars</th>
                                                <th class="text-right"><input type="date" id="profitAndLossReportDate" value="<?= $profitAndLossReportDate ?>" max="<?= date("Y-m-d") ?>"></th>
                                            </tr>
                                        </thead>
                                        <script>
                                            $(document).ready(function() {
                                                $(document).on("change", "#profitAndLossReportDate", function() {
                                                    let date = btoa(`${$(this).val()}`);
                                                    window.location.href = `<?= BASE_URL ?>branch/location/report-profit-and-loss.php?filter=${date}`;
                                                    console.log(date);
                                                });
                                            });
                                        </script>
                                        <tbody>
                                            <?php
                                            function buildTreeHTML($tree, $loop = 1)
                                            {
                                                $colorShades = ["","6d8ca9","7c98b2","8aa3ba","99afc3","a7bacb","b6c6d4","c5d1dd","d3dde5","e2e8ee","f0f4f6","ffffff"];
                                                foreach ($tree as $node) {

                                                    if ($node["typeAcc"] == 1 || $node["typeAcc"] == 2) {
                                                        continue;
                                                    }else{
                                                        $paddingSize = $loop*30;
                                                        $paddingLeftStyle = "padding-left:".$paddingSize."px;";
                                                        $backgroundStyle = "background-color:#".$colorShades[$loop].";";
                                                    }
                                                    if ($node["glStType"] == "group") {
                                                        if ($node["id"] == 3 || $node["id"] == 4) {
                                                            ?><tr>
                                                                <td class="font-bold bg-alter text-lg" style='<?= $paddingLeftStyle ?><?= $backgroundStyle ?>' ><?= $node["gl_label"] ?></td>
                                                                <td class="font-bold bg-alter text-lg" style='<?= $backgroundStyle ?>'></td>
                                                            </tr><?php
                                                        } else {
                                                            ?><tr>
                                                                <td class="font-bold bg-alter" style='<?= $paddingLeftStyle ?><?= $backgroundStyle ?>' ><?= $node["gl_label"] ?></td>
                                                                <td class="font-bold bg-alter" style='<?= $backgroundStyle ?>'></td>
                                                               
                                                            </tr><?php
                                                        }
                                                    } else {
                                                        ?><tr>
                                                            <td class="font-bold" style='<?= $paddingLeftStyle ?><?= $backgroundStyle ?>' ><?= $node["gl_label"] ?></td>
                                                            <td class="font-bold text-right" style='<?= $backgroundStyle ?>' ><?= number_format($node["gl_total_amount"], 2) ?></td>
                                                        </tr><?php
                                                    }
                                                    if (!empty($node['data'])) {
                                                        $tempLoop=$loop+1;
                                                        buildTreeHTML($node['data'], $tempLoop);
                                                    }
                                                }
                                            }
                                            buildTreeHTML($balTreeObj);
                                            ?>
                                            <!-- <tr>
                                                <td class="font-bold text-lg">EQUITY AND LIABILITY</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Shareholder's Fund :</td>
                                                <td class="font-bold bg-alter">950.52</td>
                                            </tr>
                                            <tr>
                                                <td>Share Capital</td>
                                                <td>200.00</td>
                                            </tr>
                                            <tr>
                                                <td>Reverse and Supplies</td>
                                                <td>750.05</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Long Terms Borrowings</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Deferred Tax Liability</td>
                                                <td>2.02</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current liabilities :</td>
                                                <td class="font-bold bg-alter">2.02</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Borrowings</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>Trade payables</td>
                                                <td>39.96</td>
                                            </tr>
                                            <tr>
                                                <td>Short Term Provisions</td>
                                                <td>39.96</td>

                                            </tr>
                                            <tr>
                                                <td>Other Current Liability</td>
                                                <td>39.96</td>

                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77</td>
                                            </tr> -->
                                        </tbody>
                                        <!-- <tbody>
                                            <tr>
                                                <td class="font-bold text-lg">ASSETS</td>
                                                <td></td>

                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Non-current assets :</td>
                                                <td class="font-bold bg-alter">254.27</td>

                                            </tr>
                                            <tr>
                                                <td>Fixed Assets</td>
                                                <td>71.65</td>

                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td></td>

                                            </tr>
                                            <tr>
                                                <td>Long term loans and advances</td>
                                                <td>182.62</td>

                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Current assets :</td>
                                                <td class="font-bold bg-alter">914.50</td>

                                            </tr>
                                            <tr>
                                                <td>Stock in Hand</td>
                                                <td>9.61</td>

                                            </tr>
                                            <tr>
                                                <td>Trade receivables</td>
                                                <td>802.38</td>

                                            </tr>
                                            <tr>
                                                <td>Cash and cash equivalents</td>
                                                <td>-26.80</td>

                                            </tr>
                                            <tr>
                                                <td>Current Investments</td>
                                                <td></td>

                                            </tr>
                                            <tr>
                                                <td>Other Current Assets</td>
                                                <td>30.19</td>

                                            </tr>
                                            <tr>
                                                <td>Short Term Loans and Advances</td>
                                                <td>99.12</td>

                                            </tr>
                                            <tr>
                                                <td class="font-bold bg-alter">Total :</td>
                                                <td class="font-bold bg-alter">1,168.77 </td>

                                            </tr>
                                            <tr>
                                                <td class="bg-grey text-white">Net Worth</td>
                                                <td class="bg-grey text-white">950.09</td>

                                            </tr>
                                        </tbody> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    <?php
    }
    ?>

</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
include("common/footer.php");
?>