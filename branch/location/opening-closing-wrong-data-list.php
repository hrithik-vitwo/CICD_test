<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

$dbObj = new Database();

class OpeningClosingWrongData
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $branch_gstin;

    private $dbObj;
    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $branch_gstin;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->branch_gstin = $branch_gstin;
        $this->dbObj = new Database();
    }

    public function getOneGlOpeningData($gl = 0, $subGl = "", $postingMonth = "")
    {
        // $postingDate will be YYYY-MM

        $debitObj = $this->dbObj->queryGet("SELECT SUM(erp_acc_debit.debit_amount) AS totalDebitAmt FROM erp_acc_journal LEFT JOIN erp_acc_debit ON erp_acc_journal.id = erp_acc_debit.journal_id WHERE erp_acc_debit.glId=$gl AND erp_acc_debit.subGlCode='$subGl' AND erp_acc_journal.location_id = $this->location_id AND erp_acc_journal.postingDate LIKE '$postingMonth%'");


        $creditObj = $this->dbObj->queryGet("SELECT SUM(erp_acc_credit.credit_amount) AS totalCreditAmt FROM erp_acc_journal LEFT JOIN erp_acc_credit ON erp_acc_journal.id = erp_acc_credit.journal_id WHERE erp_acc_credit.glId=$gl AND erp_acc_credit.subGlCode='$subGl' AND erp_acc_journal.location_id = $this->location_id AND erp_acc_journal.postingDate LIKE '$postingMonth%'");

        $openingClosingObj =  $this->dbObj->queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE gl= $gl AND `subgl` = '$subGl' and company_id=$this->company_id AND location_id=$this->location_id AND date LIKE '$postingMonth%'");

        $logTransactionAmt = $debitObj["data"]["totalDebitAmt"] - ($creditObj["data"]["totalCreditAmt"]);
        $summaryTransactionAmt = $openingClosingObj['data']['closing_val'] - ($openingClosingObj['data']['opening_val']);
        return [
            "logTransactionData" => [
                "debiAmt" => $debitObj["data"]["totalDebitAmt"],
                "creditAmt" => $creditObj["data"]["totalCreditAmt"],
                "transactionAmt" => $logTransactionAmt,
            ],
            "summaryTransactionData" => [
                "openingAmt" => $openingClosingObj['data']['opening_val'],
                "closingAmt" => $openingClosingObj['data']['closing_val'],
                "transactionAmt" => $summaryTransactionAmt
            ],
            "differanceAmt" => $logTransactionAmt - ($summaryTransactionAmt),
            "gl" =>$gl
        ];
    }
}

$openingClosingWrongDataObj = new OpeningClosingWrongData();

// console($chartOfAccObj['data']);
$companyOpeningDate = date("Y-m-d", strtotime($dbObj->queryGet("SELECT `opening_date` FROM `erp_companies` WHERE `company_id`=" . $company_id)["data"]["opening_date"]));
$monthList = getFirstDatesOfMonths($companyOpeningDate, date("Y-m-d"));

// console($monthList);
$chartOfAccObj = getAllChartOfAccounts_list_Account($company_id);
// console($chartOfAccObj);
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .left-sticky {
        position: sticky;
        left: 0;
    }
    
    tr.sticky-left-th:nth-child(even) td.left-sticky {
        background: #8799a9;
    }

    .position-index {
        z-index: 99;
    }

    tr.sticky-left-th td:first-child {
        background: #a2b0bc;
    }

    tr.sticky-left-th td:nth-child(1) td{
        background: #5f7285 !important;
    }
</style>
<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">Opening Closing Wrong Data List</h4>
        <div class="card">
            <div class="card-body p-0" style="overflow: auto;">
                <table id="datatable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                    <thead>
                        <tr>
                            <th class="left-sticky position-index">Gl Name</th>
                            <?php foreach ($monthList as $month) { ?>
                                <?php $monthName = getMonthFromDigits($month);
                                ?>
                                <th colspan="2" class="text-center"><?= $monthName ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th class="left-sticky position-index"></th>
                            <?php foreach ($monthList as $month) { ?>
                                <th class="text-center">As Per Log</th>
                                <th class="text-center">As Per Summary</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach ($chartOfAccObj['data'] as $oneGl) {
                        ?>
                            <tr class="sticky-left-th">
                                <td class="left-sticky"><?= $oneGl["gl_label"] ?></td>
                                <?php
                                foreach ($monthList as $month) {
                                    $dataObj = $openingClosingWrongDataObj->getOneGlOpeningData($oneGl['id'], "", explode("-", $month)[0] . "-" . explode("-", $month)[1]);
                                    // console($dataObj);
                                    if ($dataObj['differanceAmt'] !=0) {
                                ?>
                                        <td class="status bg-danger"><?= $dataObj['logTransactionData']['transactionAmt'] ?></td>
                                        <td class="status bg-danger"><?= $dataObj['summaryTransactionData']['transactionAmt'] ?></td>
                                    <?php
                                    } else { ?>
                                        <td class="status"><?= $dataObj['logTransactionData']['transactionAmt'] ?></td>
                                        <td class="status"><?= $dataObj['summaryTransactionData']['transactionAmt'] ?></td>

                                <?php
                                    }
                                } ?>
                            </tr>
                        <?php }
                        ?>

                    </tbody>
                </table>

            </div>
        </div>

    </section>
</div>

<?php
require_once("../common/footer.php");


function getFirstDatesOfMonths($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->modify('first day of next month');

    $dates = [];
    while ($start < $end) {
        $dates[] = $start->format('Y-m-d');
        $start->modify('first day of next month');
    }
    return $dates;
}


function getMonthFromDigits($digits)
{
    $monthNumber = explode("-", $digits)[1];
    $year = explode("-", $digits)[0];

    // Define an array of months
    $months = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    return isset($months[$monthNumber]) ? $months[$monthNumber] . " $year" : 'Invalid month' . $monthNumber;
}

?>