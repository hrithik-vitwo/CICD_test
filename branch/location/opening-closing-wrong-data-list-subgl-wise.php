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

class OpeningClosingWrongData
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

        $tnxObj = $this->dbObj->queryGet("SELECT subGlCode, month, SUM(credit_amount) AS totalCreditAmt, SUM(debit_amount) AS totalDebitAmt, (SUM(debit_amount)-SUM(credit_amount)) AS totalClosingAmt FROM ((SELECT
                                                cr.glId,
                                                IF(cr.subGlCode='0', '', cr.subGlCode) as subGlCode,
                                                cr.subGlName,
                                                cr.credit_amount AS credit_amount,
                                                0 AS debit_amount,
                                                'cr' AS tnxType,
                                                j.id as journal_id,
                                                j.company_id,
                                                j.branch_id,
                                                j.location_id,
                                                j.postingDate,
                                                SUBSTR(j.postingDate, 1, 7) AS month                                                                                          
                                            FROM
                                                erp_acc_credit AS cr LEFT JOIN `erp_acc_journal` as j ON cr.journal_id=j.id WHERE j.location_id=$this->location_id
                                            )
                                                UNION ALL
                                            (
                                            SELECT
                                                dr.glId,
                                                IF(dr.subGlCode='0', '', dr.subGlCode) as subGlCode,
                                                dr.subGlName,
                                                0 AS credit_amount,
                                                dr.debit_amount AS debit_amount,
                                                'dr' AS tnxType,
                                                j.id as journal_id,
                                                j.company_id,
                                                j.branch_id,
                                                j.location_id,
                                                j.postingDate,
                                                SUBSTR(j.postingDate, 1, 7) AS month
                                            FROM
                                                erp_acc_debit AS dr LEFT JOIN `erp_acc_journal` as j ON dr.journal_id=j.id WHERE j.location_id=$this->location_id
                                            )) AS resultTable WHERE subGlCode!='' GROUP BY subGlCode, month", true);

        foreach ($tnxObj["data"] as $row) {
            $this->transactionData[$row["subGlCode"]][$row["month"]] = $row;
        }


        $openObj = $this->dbObj->queryGet("SELECT `subgl`, `date`, `opening_val`, `closing_val` FROM `$this->erp_opening_closing_balance_tbl` WHERE `location_id`=$this->location_id AND `subgl`!='' ORDER BY `subgl`, `date`", true);
        foreach ($openObj["data"] as $row) {
            $yyyymm = substr($row["date"], 0, 7);
            $this->openingData[$row["subgl"]][$yyyymm] = $row;
        }
    }
    public function getFirstDatesOfMonths($endDate)
    {
        $start = new DateTime($this->companyOpeningDate);
        $end = new DateTime($endDate);
        $end->modify('last day of next month');

        $dates = [];
        while ($start < $end) {
            $dates[] = $start->format('Y-m-d');
            $start->modify('last day of next month');
        }
        return $dates;
    }

    public function getAllSubGlList()
    {


        // return $this->dbObj->queryGet("SELECT subGlList.*, masterList.parentGlId, masterList.type FROM (SELECT DISTINCT subGlCode, subGlName FROM ((SELECT DISTINCT cr.subGlCode, cr.subGlName FROM erp_acc_credit AS cr LEFT JOIN `erp_acc_journal` AS j ON cr.journal_id = j.id WHERE j.location_id = $this->location_id) UNION ALL (SELECT DISTINCT dr.subGlCode, dr.subGlName FROM erp_acc_debit AS dr LEFT JOIN `erp_acc_journal` AS j ON dr.journal_id = j.id WHERE j.location_id = $this->location_id)) AS resultTable WHERE 1 AND subGlCode != 0 ORDER BY subGlCode) AS subGlList LEFT JOIN (SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $this->company_id UNION ALL SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $this->company_id UNION ALL SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $this->company_id UNION ALL SELECT acc_code AS code, bank_name AS name, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $this->company_id UNION ALL SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $this->company_id) AS masterList ON subGlList.subGlCode = masterList.code ORDER BY subGlList.subGlCode", true);

        return $this->dbObj->queryGet("SELECT masterList.* FROM ( SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $this->company_id UNION ALL SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $this->company_id UNION ALL SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $this->company_id UNION ALL SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $this->company_id UNION ALL SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $this->company_id ) AS masterList",true);
        
        //return $this->dbObj->queryGet("SELECT DISTINCT subGlCode, subGlName FROM ((SELECT DISTINCT cr.subGlCode, cr.subGlName FROM erp_acc_credit AS cr LEFT JOIN `erp_acc_journal` as j ON cr.journal_id=j.id WHERE j.location_id=$this->location_id ) UNION ALL ( SELECT DISTINCT dr.subGlCode, dr.subGlName FROM erp_acc_debit AS dr LEFT JOIN `erp_acc_journal` as j ON dr.journal_id=j.id WHERE j.location_id=$this->location_id )) AS resultTable WHERE 1 AND subGlCode!=0 ORDER BY subGlCode", true);
    }

    public function getSubglOpeningBalance($subGlCode, $yyyymm)
    {
        return $this->dbObj->queryGet("SELECT `subgl`, `date`, `opening_val`, `closing_val` FROM `$this->erp_opening_closing_balance_tbl` WHERE `location_id`=$this->location_id AND `subgl`='" . $subGlCode . "' AND `date` LIKE '$yyyymm%'");
    }


    public function getTnxData($subGl, $month)
    {
        $yyyymm = substr($month, 0, 7);
        return $this->transactionData[$subGl][$yyyymm] ?? [
            "subGlCode" => $subGl,
            "month" => $month,
            "totalCreditAmt" => 0,
            "totalDebitAmt" => 0,
            "totalClosingAmt" => 0
        ];
    }

    public function getOpenData($subGl, $month)
    {
        $yyyymm = substr($month, 0, 7);
        return $this->openingData[$subGl][$yyyymm] ?? [
            "subgl" => $subGl,
            "date" => $month,
            "opening_val" => 0,
            "closing_val" => 0
        ];
    }

    public function printLog()
    {
        // console($this->openingData);
        // console($this->transactionData);
    }
}
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

    tr.sticky-left-th td:nth-child(1) td {
        background: #5f7285 !important;
    }
</style>

<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-2 mb-0">Opening Closing Wrong Data List Sub GL wise</h4>
        <span class="text-xs">Company: <?= $company_id ?>, Branch: <?= $branch_id ?>, Location: <?= $location_id ?>, Opening Date: <?= $compOpeningDate ?></span>
        <?php
        $openingClosingWrongDataObj = new OpeningClosingWrongData();
        console($openingClosingWrongDataObj->printLog());
        $monthList = $openingClosingWrongDataObj->getFirstDatesOfMonths(date("Y-m-d"));
        // console($monthList);
        $subGlObj = $openingClosingWrongDataObj->getAllSubGlList();
        // console($subGlObj);
        ?>
        <div class="card mt-2">
            <div class="card-body p-0" style="overflow: auto;">
                <table id="datatable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                    <thead>
                        <tr>
                            <th rowspan="2" class="left-sticky position-index">Gl</th>
                            <th rowspan="2" class="left-sticky position-index">Sub Gl</th>
                            <th rowspan="2" class="left-sticky position-index">Name</th>
                            <th rowspan="2" class="left-sticky position-index">Opening</th>
                            <?php foreach ($monthList as $month) { ?>
                                <?php $monthName = getMonthFromDigits($month);
                                ?>
                                <th colspan="2" class="text-center"><?= $monthName ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php foreach ($monthList as $month) { ?>
                                <th class="text-center">Log</th>
                                <th class="text-center">Computed</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($subGlObj["data"] as $subGlKey => $subGl) {
                            $yyyymm = substr($monthList[0], 0, 7);
                            $subglOpenBalObj = $openingClosingWrongDataObj->getSubglOpeningBalance($subGl["subGlCode"], $yyyymm);
                            $subglOpenBal = floatval($subglOpenBalObj["data"]["opening_val"] ?? 0);
                            // console($openingBalanceObj);
                            // $pevClosing = 0;
                            $pevClosing = $subglOpenBal;
                        ?>
                            <form action="" method="post">
                                <tr id="tableRow_<?= $subGlKey ?>">
                                    <td><?= $subGl["parentGlId"] ?></td>
                                    <td><?= $subGl["subGlCode"] ?></td>
                                    <td class="pre-normal"><?= $subGl["subGlName"] ?></td>
                                    <td class="text-right"><?= number_format($subglOpenBal, 2) ?></td>
                                    <?php
                                    $formData = [];
                                    $isNeedToResolve = false;
                                    $monthLastKey = count($monthList) - 1;
                                    foreach ($monthList as $monthKey => $month) {
                                        $tnxData = $openingClosingWrongDataObj->getTnxData($subGl["subGlCode"], $month);
                                        $openData = $openingClosingWrongDataObj->getOpenData($subGl["subGlCode"], $month);

                                        $formData[$subGl["subGlCode"]][$month]["glId"] = $subGl["parentGlId"];
                                        $formData[$subGl["subGlCode"]][$month]["openAmount"] = round($pevClosing, 4);

                                        $pevClosing = $pevClosing + $tnxData["totalDebitAmt"] - $tnxData["totalCreditAmt"];

                                        $formData[$subGl["subGlCode"]][$month]["logAmount"] = round($pevClosing, 4);
                                        $formData[$subGl["subGlCode"]][$month]["computedAmount"] = round($openData["closing_val"], 4);
                                        $formData[$subGl["subGlCode"]][$month]["monthType"] = ($monthKey === 0 ? "first" : ($monthKey === $monthLastKey ? "last" : "middle"));

                                        $openAmount = number_format($openData["closing_val"], 4);
                                        $tnxAmount = number_format($pevClosing, 4);
                                        // console($tnxData["totalCreditAmt"]);
                                        // [
                                        //     "subGlCode" => 000606000614
                                        //     "month" => 2023-10
                                        //     "totalCreditAmt" => 2126118.7400
                                        //     "totalDebitAmt" => 2126118.7300
                                        //     "totalClosingAmt" => -0.0100
                                        // ]

                                        // [
                                        //     "subgl" => "",
                                        //     "date" => "",
                                        //     "opening_val" => 0,
                                        //     "closing_val" => 0
                                        // ];

                                        if ($monthKey != $monthLastKey) {
                                            $cssClass = abs($openData["closing_val"] - $pevClosing) > 1 ? "bg-danger" : "";
                                            if ($cssClass != "") {
                                                $isNeedToResolve = true;
                                            }
                                    ?>
                                            <td class="text-right <?= $cssClass ?> pre-normal p-0" style="border-left: 1px solid white;">
                                                <?= $tnxAmount ?>
                                            </td>
                                            <td class="text-right <?= $cssClass ?> p-0">
                                                <?= $openAmount ?>
                                            </td>
                                        <?php
                                        } else {
                                        ?>
                                            <td style="border-left: 1px solid white;" colspan="2">
                                                <?php
                                                if ($isNeedToResolve) {
                                                ?>
                                                    <input type="hidden" name="formData" id="formData_<?= $subGlKey ?>" value='<?= json_encode($formData) ?>'>
                                                    <span class="btn btn-primary resolveOpeningTableBtn" id="resolveOpeningTableBtn_<?= $subGlKey ?>">Resolve</span>
                                                <?php
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tr>
                            </form>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>


<?php
require_once("../common/footer.php");


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


<script>
    $(document).ready(function() {
        let isSubmittingForm = false;
        $(document).on("click", ".resolveOpeningTableBtn", function() {
            if (!isSubmittingForm) {
                isSubmittingForm = true;
                let subGlKey = ($(this).attr("id")).split("_")[1];
                let formData = $(`#formData_${subGlKey}`).val();
                console.log({
                    formData
                });
                $.ajax({
                    type: "POST",
                    url: "opening-closing-wrong-data-list-subgl-wise-ajax.php",
                    data: {
                        'formData': formData
                    },
                    beforeSend: function() {
                        $(`#resolveOpeningTableBtn_${subGlKey}`).html("Resolving...");
                    },
                    success: function(data) {
                        let response = JSON.parse(data);
                        console.log(response);
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: response.status,
                            title: response.message
                        });
                        if (response.status == "success") {
                            $(`#tableRow_${subGlKey}`).remove();
                        }
                    },
                    error: function() {
                        console.log("Error!");
                    },
                    complete: function(jqXHR, textStatus) {
                        isSubmittingForm = false;
                        $(`#resolveOpeningTableBtn_${subGlKey}`).html("Resolve");
                        console.log("Complete!", jqXHR);
                        if (jqXHR.status != 200) {
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `warning`,
                                title: `Something went wrong, try again!`
                            });
                        }
                    }
                });
            }
        });
    })
</script>