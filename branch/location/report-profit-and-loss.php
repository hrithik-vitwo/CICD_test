<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
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
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">


<style>
    .content-wrapper {
        overflow: visible;
        height: auto;
    }

    body {
        /* Or whatever the parent selector is */
        overflow: visible;
        height: auto;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Profit and Loss</a></li>
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
                        <div class="action-btns-sheet d-flex p-0 m-0 mb-4" style="justify-content: flex-end;">
                            <?php
                            $filterObj = (isset($_GET["filter"]) && $_GET["filter"] != "") ? json_decode(base64_decode($_GET["filter"]), true) : [];
                            $reportStartDate = $filterObj["reportStartDate"] ?? date("Y-m-01");
                            $reportEndDate = $filterObj["reportEndDate"] ?? date("Y-m-d");
                            ?>
                            <button onclick="exportPLToExcel()" class="btn btn-primary mr-3 ml-2">Export to Excel</button>

                            <button class="btn btn-primary mr-3" onclick="window.print();return false;" id="printButton">Print</button>
                            <input type="date" value="<?= $reportStartDate ?>" max="<?= date("Y-m-01") ?>" id="reportStartDate" class="form-control" style="height: 30px; width: 143px;">
                            <span class="px-1">To</span>
                            <input type="date" value="<?= $reportEndDate ?>" max="<?= date("Y-m-d") ?>" id="reportEndDate" class="form-control" style="height: 30px; width: 143px;">
                            <button class="btn btn-sm btn-primary ml-1" style="height:30px;" id="applyDateFilter">Apply</button>
                            <div class="btn-group btn-group-toggle col-2 pr-0" data-toggle="buttons">
                                <label class="btn btn-secondary active">
                                    <input type="radio" class="expand_collapse" name="expand_collapse" value="collapse" autocomplete="off" checked>Collapse
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" class="expand_collapse" name="expand_collapse" value="expand" autocomplete="off">Expand
                                </label>
                            </div>
                        </div>
                        <div class="printable-blncsheet">
                            <?php
                            $glSummaryObj = new GlSummary("location", false);
                            $glSummaryObj->setGlAccTypes([3, 4]);
                            // $glSummaryObj->setGlId(6);
                            $glSummaryObj->setGlTreeView(true);
                            $glSummaryData = $glSummaryObj->getSummary($reportStartDate, $reportEndDate);
                            // $profitBeforeTax = ($glSummaryData["data"][0]["children_summary"]["closing_val"] ?? 0) + ($glSummaryData["data"][1]["children_summary"]["closing_val"] ?? 0);
                            $profitBeforeTax = ($glSummaryData["grandTotal"]["grandTotalDebit"] ?? 0) - ($glSummaryData["grandTotal"]["grandTotalCredit"] ?? 0);
                            // console(["Summary=",$glSummaryData["grandTotal"]["grandTotalDebit"], $glSummaryData["grandTotal"]["grandTotalCredit"], $profitBeforeTax]);
                            // console($glSummaryData);
                            ?>
                            <p class="text-center h6"><?= $companyNameNav ?></p>
                            <p class="text-center h5">Profit and Loss</p>
                            <p class="text-center text-sm"><?= date("d-m-Y", strtotime($glSummaryData["start_date"])) ?> To <?= date("d-m-Y", strtotime($glSummaryData["end_date"])) ?></p>
                            <hr class="mt-3 mb-0 p-0">
                            <p class="d-flex text-muted text-sm"><span class="mr-auto">Account</span> <span class="ml-auto">Total</span></p>
                            <hr class="p-0 mt-0">
                            <?php
                            function generateTreeViewHtml($data)
                            {
                                global $reportStartDate, $reportEndDate;
                                $accordionId = time() . rand(9999, 1000); ?>
                                <div id="accordion_<?= $accordionId ?>" class="p-0 ">
                                    <?php foreach ($data as $key => $oneData) {
                                        $collapseId = $accordionId . "_" . $key;
                                        $itemIsGroup = isset($oneData["children_summary"]) ? true : false;
                                        // $closingBalance = $itemIsGroup ? $oneData["children_summary"]["closing_val"] : $oneData["closing_val"];
                                        $closingBalance = $itemIsGroup ? $oneData["children_summary"]["debit_amount"] - $oneData["children_summary"]["credit_amount"] : $oneData["debit_amount"] - $oneData["credit_amount"];
                                    ?>
                                        <div class="card mb-2 bg-light text-dark">
                                            <div class="card-header p-0 d-flex bg-light text-dark accordionGlRowHover">
                                                <a class="btn collapsed mr-auto" data-toggle="collapse" data-target="#collapse_<?= $collapseId ?>" aria-expanded="false">
                                                    <p class="text-sm account font-weight-bold <?= $itemIsGroup ? "btn-link text-primary" : "text-dark" ?>"><?= $oneData["gl_label"] ?> <?php echo !empty($oneData["gl_code"]) ? '[' . $oneData["gl_code"] . ']' : '';  ?></p>
                                                </a>
                                                <?php if ($itemIsGroup) { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><?= ($closingBalance < 0) ? "(" . decimalValuePreview($closingBalance) . ")" : decimalValuePreview($closingBalance) ?></p>
                                                <?php } else { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><a target="_blank" href="<?= LOCATION_URL ?>manage-daybook.php?mode=sablager&glId=<?= $oneData["id"]; ?>&from_date=<?= $reportStartDate ?>&to_date=<?= $reportEndDate ?>"><?= ($closingBalance < 0) ? "(" . decimalValuePreview($closingBalance) . ")" : decimalValuePreview($closingBalance) ?></a></p>
                                                <?php } ?>
                                                <!-- <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><?= ($closingBalance < 0) ? "(" . decimalValuePreview($closingBalance) . ")" : decimalValuePreview($closingBalance) ?></p> -->
                                            </div>
                                            <div id="collapse_<?= $collapseId ?>" class="accordion-collapse collapse bg-light text-dark">
                                                <?php if ($itemIsGroup) { ?>
                                                    <div class="pt-2 pl-4">
                                                        <?php generateTreeViewHtml($oneData["children"]); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php }
                            generateTreeViewHtml($glSummaryData["data"]);
                            ?>
                            <hr class="mt-2 mb-0 p-0">
                            <p class="d-flex text-muted text-sm"><span class="mr-auto final-acc">(Profit) / Loss</span> <span class="ml-auto final-total"><?= ($profitBeforeTax < 0) ? "(" . decimalValuePreview($profitBeforeTax) . ")" : decimalValuePreview($profitBeforeTax) ?></span></p>
                            <hr class="p-0 mt-0">
                        </div>
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

        $("#applyDateFilter").click(function() {
            let filter = {
                reportStartDate: $("#reportStartDate").val(),
                reportEndDate: $("#reportEndDate").val()
            };
            window.location.href = `?filter=${btoa(JSON.stringify(filter))}`;
        });
    });

    function exportPLToExcel() {
        const data = [
            ["Account", "Total"]
        ];

        // Traverse all account rows using jQuery
        $('.printable-blncsheet .card-header').each(function() {
            const label = $(this).find('p.account ').text().trim();
            const amount = $(this).find('p.mr-2').text().trim();
            data.push([label, amount]);
        });

        // Add final Profit/Loss row
        const plLabel = $('.printable-blncsheet .text-muted.text-sm span.final-acc').text().trim();
        const plAmount = $('.printable-blncsheet .text-muted.text-sm span.final-total').text().trim();
        data.push([plLabel, plAmount]);

        // Generate Excel
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Profit and Loss");
        XLSX.writeFile(wb, 'profit_and_loss.xlsx');
    }
</script>