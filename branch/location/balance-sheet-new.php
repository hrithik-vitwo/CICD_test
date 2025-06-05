<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");


class LinkDrillDownController
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;

    private $balanceSheetLinkData = [];

    function __construct()
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->location_id = $location_id;
        $this->branch_id = $branch_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }

    function getBalanceSheetLink($id, $extraParams = "")
    {
        if (count($this->balanceSheetLinkData) == 0) {
            //echo $id;
            $sql = queryGet("SELECT parentGlId AS gl, CASE WHEN goodsType = 9 THEN 'asset-report.php' WHEN goodsType = 7 THEN 'servicepurchase-report.php'  WHEN goodsType = 5 THEN 'sarvicesale-report.php' ELSE 'manage-inventory.php?bs' END AS link
            FROM erp_inventory_items  WHERE `company_id`= $this->company_id AND `location_id` = $this->location_id  GROUP BY parentGlId,goodsType 
            UNION
            SELECT parentGlId AS gl, 'ledger-view-report.php' AS 'link' FROM erp_customer WHERE `company_id`= $this->company_id AND `location_id` = $this->location_id GROUP BY parentGlId
            UNION
            SELECT parentGlId AS gl, 'accountingreports/payable-analysis.php?detailed-view' AS 'link' FROM erp_vendor_details WHERE `company_id`= $this->company_id AND `location_id` = $this->location_id GROUP BY parentGlId
            UNION
            SELECT parent_gl AS gl, 'banking-visualize-chart.php?bs' AS 'link' FROM erp_acc_bank_cash_accounts WHERE `company_id`= $this->company_id  GROUP BY parent_gl", true);

            $this->balanceSheetLinkData =  $sql["data"];
        }
        $href = '';
        foreach ($this->balanceSheetLinkData as $data) {
            if ($data['gl'] == $id) {
                $href = $data['link'];
            }
        }
        
        return $href != "" ? "target='_blank' href = '" . BASE_URL . "branch/location/" . $href . $extraParams . "'" : "";
    }
}


$linkDrillDownControllerObj = new LinkDrillDownController();

?>


<style>
    .accordionGlRowHover:hover {
        background-color: #00306026 !important;
    }

    td.font-bold.bg-alter {
        background: #afc1d2;
    }

    td.bg-grey.text-white {
        background: #003060;
    }

    .blnc-sheet-card {
        background: #fff;
    }

    .blnc-sheet-card table thead tr th {
        vertical-align: middle;
    }

    .blnc-sheet-card table tbody tr td {
        background: #fff;
    }

    .blnc-sheet-card table tbody tr:nth-child(2n+1) td {
        background: #e7f2fd;
        border-color: #e7f2fd;
    }

    /* .liability-table tbody tr:nth-child(2n+1) td,
    .assets-table tbody tr:nth-child(2n+1) td {
        
    } */

    .filter-date {
        max-width: 200px;
        margin: 0 0;
        float: right;
    }

    .print-padding-none {
        padding: 0 200px !important;
    }

    @media print {
        body {
            visibility: hidden;
        }

        .printable-blncsheet {
            visibility: visible !important;
        }

        .print-padding-none {
            padding: 0px 0px !important;
        }
    }

    @media only screen and (max-width: 1023px) {
        .print-padding-none {
            padding: 0 !important;
        }

        .breadcrumb {
            margin-top: 50px;
        }
    }

    @media (min-width: 768px) and (max-width: 1023px) {}

    @media (min-width: 980px) and (max-width: 1023px) {}
</style>
<link rel="stylesheet" href="../../public/assets/new_listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Balance Sheet</a></li>
                <!-- <li class="breadcrumb-item active">
                    <a href="manage-inventory.php?post-grn" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add New</a>
                </li> -->
                <li class="back-button">
                    <a href="">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>
        </div>
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="print-padding-none row p-0 m-0">
                        <div class="action-btns-sheet d-flex flex-wrap p-0 m-0 mb-4">
                            <?php
                            $balanceSheetReportDate = (isset($_GET["filter"]) && $_GET["filter"] != "") ? base64_decode($_GET["filter"]) : date("Y-m-d");
                            $balanceSheetReportDate = (date('Y-m-d', strtotime($balanceSheetReportDate)) == $balanceSheetReportDate) ? $balanceSheetReportDate : date("Y-m-d");
                            $todayDate = date_create(date("Y-m-d"));
                            $filterDate = date_create($balanceSheetReportDate);
                            $diffObj = date_diff($todayDate, $filterDate);
                            $diffDays = $diffObj->format("%R%a");
                            if ($diffDays > 0) {
                                $balanceSheetReportDate = date("Y-m-d");
                            }
                            ?>
                            <div class="balancesheet_inner d-flex flex-wrap">
                                <p class="mr-3">Hide zero value Gl <input type="checkbox" id="zeroValGlHiddenCheckBox" name="zeroValGlHiddenCheckBox" <?= (isset($_GET["zeroValGlHide"]) && $_GET["zeroValGlHide"] == "false") ? "" : "checked" ?> class="p-1 m-1 commonFilter"></p>
                                <button class="btn btn-primary mr-3" onclick="window.print();return false;" id="printButton">Print</button>
                            </div>
                            <div class="balancesheet_inner">
                                <input type="date" value="<?= $balanceSheetReportDate ?>" max="<?= date("Y-m-d") ?>" id="reportStartDate" class="form-control commonFilter">
                                <div class="btn-group btn-group-toggle pr-0" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                        <input type="radio" class="expand_collapse" name="expand_collapse" value="collapse" autocomplete="off" checked>Collapse
                                    </label>
                                    <label class="btn btn-secondary">
                                        <input type="radio" class="expand_collapse" name="expand_collapse" value="expand" autocomplete="off">Expand
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="printable-blncsheet">
                            <?php
                            $glSummaryObj = new GlSummary("location");
                            $glSummaryObj->setGlAccTypes([1, 2]);
                            // $glSummaryObj->setGlId(6);
                            $glSummaryObj->setGlTreeView(true);
                            // $glSummaryData = $glSummaryObj->getSummary($balanceSheetReportDate);
                            $glSummaryData = $glSummaryObj->getSummaryTree(null, $balanceSheetReportDate);
                            // console($glSummaryData);
                            //For Profit before tax

                            $glSummaryObj1 = new GlSummary("location", false);
                            $glSummaryObj1->setGlAccTypes([3, 4]); // income and espense
                            // $glSummaryForProfitAndLoss = $glSummaryObj1->getSummary(date("Y-m-01", strtotime($balanceSheetReportDate)), $balanceSheetReportDate);
                            $glSummaryForProfitAndLossObj = $glSummaryObj1->getSummary(null, $balanceSheetReportDate);

                            $glSummaryForProfitAndLossVal = ($glSummaryForProfitAndLossObj["data"][0]["children_summary"]["closing_val"] ?? 0) + ($glSummaryForProfitAndLossObj["data"][1]["children_summary"]["closing_val"] ?? 0);

                            $glSummaryForOpeningBalObj = new GlSummary("location");
                            $glSummaryForOpeningBalObj->setGlAccTypes([1, 2, 3, 4]);
                            $glSummaryForOpeningBalObj->setGlTreeView(false);
                            $glSummaryForOpeningBalanceObj = $glSummaryForOpeningBalObj->getSummary(null, $balanceSheetReportDate);
                            $glSummaryForOpeningBalance = $glSummaryForOpeningBalanceObj["grandTotal"]["grandTotalOpening"] ?? 0;
                            $glSummaryForOpeningBalance = $glSummaryForOpeningBalance == 0 ? $glSummaryForOpeningBalance : $glSummaryForOpeningBalance * -1;
                            // console($glSummaryForOpeningBalanceObj);
                            // console([date("Y-m-01", strtotime($balanceSheetReportDate)),$balanceSheetReportDate]);
                            // console(["glSummaryForProfitAndLossVal", $glSummaryForProfitAndLossVal]);
                            // console(["glSummaryForOpeningBalance", $glSummaryForOpeningBalance]);
                            // console($glSummaryData["data"]);
                            // console($glSummaryForProfitAndLoss);

                            $updatingClosingForGlId = 121;
                            function findPathByIdAndUpdate(&$nodes, $targetId, $updateValue = 0)
                            {
                                foreach ($nodes as &$node) {
                                    if ($node['id'] == $targetId) {
                                        $node['closing_val'] += $updateValue;
                                        return true;
                                    }
                                    if (!empty($node['children']) && findPathByIdAndUpdate($node['children'], $targetId, $updateValue)) {
                                        $node['children_summary']['closing_val'] += $updateValue;
                                        return true;
                                    }
                                }
                                return false;
                            }
                            findPathByIdAndUpdate($glSummaryData["data"], $updatingClosingForGlId, $glSummaryForProfitAndLossVal);


                            ?>

                            <p class="text-center h6"><?= $companyNameNav ?></p>
                            <p class="text-center h5">Balance Sheet</p>
                            <p class="text-center text-sm"><?= date("d-m-Y", strtotime($glSummaryData["end_date"])) ?></p>
                            <hr class="mt-3 mb-0 p-0">
                            <p class="d-flex text-muted text-sm"><span class="mr-auto">Account</span> <span class="ml-auto">Total</span></p>
                            <hr class="pt-0 mt-0">
                            <?php
                            $isZeroValGlHide = (isset($_GET["zeroValGlHide"]) && $_GET["zeroValGlHide"] == "false") ? false : true;
                            function generateTreeViewHtml($data)
                            {
                                global $balanceSheetReportDate;
                                global $glSummaryForOpeningBalance;
                                global $isZeroValGlHide;
                                global $linkDrillDownControllerObj;
                                global $compOpeningDate;
                                // console($data);
                                $accordionId = time() . rand(9999, 1000); ?>
                                <div id="accordion_<?= $accordionId ?>" class="p-0 ">
                                    <?php foreach ($data as $key => $oneData) {
                                        $collapseId = $accordionId . "_" . $key;
                                        $itemIsGroup = count($oneData["children_summary"]) > 0 ? true : false;
                                        $closingBalance = $itemIsGroup ? $oneData["children_summary"]["closing_val"] : $oneData["closing_val"];

                                        if ($oneData["id"] == 2 || $oneData["id"] == 107) {
                                            $closingBalance += $glSummaryForOpeningBalance;
                                        }

                                        if ($isZeroValGlHide && $closingBalance == 0) {
                                            continue;
                                        }


                                    ?>
                                        <div class="card mb-2 bg-light text-dark">
                                            <div class="card-header p-0 d-flex bg-light text-dark accordionGlRowHover">
                                                <a class="btn collapsed mr-auto" data-toggle="collapse" data-target="#collapse_<?= $collapseId ?>" aria-expanded="false">
                                                    <p class="text-sm font-weight-bold <?= $itemIsGroup ? "btn-link text-primary" : "text-dark" ?>"><?= $oneData["gl_label"] ?> <?php echo !empty($oneData["gl_code"]) ? '[' . $oneData["gl_code"] . ']' : '';  ?></p>
                                                    </p>
                                                </a>
                                                <?php if ($itemIsGroup) { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><?= ($closingBalance < 0) ? "(" . number_format(abs($closingBalance), 2) . ")" : number_format($closingBalance, 2) ?></p>
                                                <?php } else if ($oneData["id"] == "") { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><a><?= ($closingBalance < 0) ? "(" . number_format(abs($closingBalance), 2) . ")" : number_format($closingBalance, 2) ?></a></p>
                                                <?php } else {
                                                    $extraParams = "?gl=".$oneData["id"]."&toDate=" . $balanceSheetReportDate;
                                                    // echo $oneData["id"];
                                                    // $fullLink = $linkDrillDownControllerObj->getBalanceSheetLink($oneData["id"], $extraParams);

                                                    // $fullLink = $fullLink != "" ? "target='_blank' href = '".BASE_URL . "branch/location/ledger-view-report.php?gl=".$oneData["id"]."&fromDate=".$compOpeningDate."&toDate=".$balanceSheetReportDate."'" : "";


                                                    $fullLink = "target='_blank' href = '".BASE_URL . "branch/location/ledger-view-report.php?gl=".$oneData["id"]."&fromDate=".$compOpeningDate."&toDate=".$balanceSheetReportDate."'";

                                                ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><a <?= $fullLink ?>><?= ($closingBalance < 0) ? "(" . number_format(abs($closingBalance), 2) . ")" : number_format($closingBalance, 2) ?></a></p>
                                                <?php } ?>
                                            </div>
                                            <div id="collapse_<?= $collapseId ?>" class="accordion-collapse collapse bg-light text-dark">
                                                <?php if ($itemIsGroup) { ?>
                                                    <div class="pt-2 pl-4">
                                                        <?php
                                                        if ($oneData["id"] == 107) {
                                                            $oneData["children"][] = [
                                                                "id" => 9876567,
                                                                "p_id" => 107,
                                                                "gl_code" => "",
                                                                "gl_label" => "Opening Balance Adjustment",
                                                                "status" => "active",
                                                                "glStType" => "group",
                                                                "typeAcc" => 2,
                                                                "opening_val" => 0,
                                                                "debit_amount" => 0,
                                                                "credit_amount" => 0,
                                                                "closing_val" => 0,
                                                                "children" => [
                                                                    [

                                                                        "id" => "",
                                                                        "p_id" => 9876567,
                                                                        "gl_code" => 50000,
                                                                        "gl_label" => "Opening Balance Adjustment",
                                                                        "status" => "active",
                                                                        "glStType" => "account",
                                                                        "typeAcc" => 2,
                                                                        "opening_val" => 0,
                                                                        "debit_amount" => 0,
                                                                        "credit_amount" => 0,
                                                                        "closing_val" => $glSummaryForOpeningBalance,

                                                                    ]
                                                                ],
                                                                "children_summary" => ["closing_val" => $glSummaryForOpeningBalance]
                                                            ];
                                                        }
                                                        generateTreeViewHtml($oneData["children"]); ?>
                                                    </div>
                                                <?php }
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php }
                            generateTreeViewHtml($glSummaryData["data"]);
                            ?>
                        </div>
                        <!-- <div class="grandTotal row p-0 m-0">
                            <p class="text-right text-sm pr-2">Total 20000000.00</p>
                        </div>
                         -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
include("../common/footer.php");
?>

<script>
    $(document).ready(function() {
        $('.expand_collapse').click(function() {
            if ($(this).val() == "expand") {
                $('.accordion-collapse').addClass('show');
            } else {
                $('.accordion-collapse').removeClass('show');
            }
        });

        // $(document).on("change", "#reportStartDate", function() {
        //     let date = btoa(`${$(this).val()}`);
        //     window.location.href = `?filter=${date}`;
        // });

        $(document).on("change", ".commonFilter", function() {
            let date = btoa($("#reportStartDate").val());
            let zeroValGlHiddenCheckBoxChecked = $("#zeroValGlHiddenCheckBox").is(":checked");
            window.location.href = `?filter=${date}&zeroValGlHide=${zeroValGlHiddenCheckBoxChecked}`;
        });

    });
</script>