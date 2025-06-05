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
                        <div class="action-btns-sheet d-flex p-0 m-0 mb-4" style="justify-content: flex-end;">
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
                            <p class="mr-3">Hide zero value Gl <input type="checkbox" id="zeroValGlHiddenCheckBox" name="zeroValGlHiddenCheckBox" <?= (isset($_GET["zeroValGlHide"]) && $_GET["zeroValGlHide"]=="false") ? "" : "checked" ?> class="p-1 m-1 commonFilter"></p>
                            <button class="btn btn-primary mr-3" onclick="window.print();return false;" id="printButton">Print</button>
                            <input type="date" value="<?= $balanceSheetReportDate ?>" max="<?= date("Y-m-d") ?>" id="reportStartDate" class="form-control commonFilter" style="height: 30px; width: 143px;">
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
                            $glSummaryObj = new GlSummary("location");
                            $glSummaryObj->setGlAccTypes([1, 2]);
                            // $glSummaryObj->setGlId(6);
                            $glSummaryObj->setGlTreeView(true);
                            $glSummaryData = $glSummaryObj->getSummary($balanceSheetReportDate);
                            // console($glSummaryData);
                            //For Profit before tax

                            $glSummaryObj1 = new GlSummary("location", false);
                            $glSummaryObj1->setGlAccTypes([3, 4]); // income and espense
                            $glSummaryForProfitAndLoss = $glSummaryObj1->getSummary(date("Y-m-01", strtotime($balanceSheetReportDate)),$balanceSheetReportDate);

                            $openingBalAdjustmentVal = ($glSummaryForProfitAndLoss["data"][0]["children_summary"]["closing_val"] ?? 0) + ($glSummaryForProfitAndLoss["data"][1]["children_summary"]["closing_val"] ?? 0);

                            // console([date("Y-m-01", strtotime($balanceSheetReportDate)),$balanceSheetReportDate]);
                            // console(["profitBeforeTax", $openingBalAdjustmentVal]);
                            // console($glSummaryForProfitAndLoss);

                            $updatingClosingForGlId = 121;
                            function findPathByIdAndUpdate(&$nodes, $targetId, $updateValue=0) {
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
                            findPathByIdAndUpdate($glSummaryData["data"], $updatingClosingForGlId, $profitBeforeTax);


                            ?>

                            <p class="text-center h6"><?= $companyNameNav ?></p>
                            <p class="text-center h5">Balance Sheet</p>
                            <p class="text-center text-sm"><?= date("d-m-Y", strtotime($glSummaryData["start_date"])) ?></p>
                            <hr class="mt-3 mb-0 p-0">
                            <p class="d-flex text-muted text-sm"><span class="mr-auto">Account</span> <span class="ml-auto">Total</span></p>
                            <hr class="pt-0 mt-0">
                            <?php
                            $isZeroValGlHide=(isset($_GET["zeroValGlHide"]) && $_GET["zeroValGlHide"]=="false") ? false : true;
                            function generateTreeViewHtml($data)
                            {
                                global $balanceSheetReportDate;
                                global $openingBalAdjustmentVal;
                                global $isZeroValGlHide;
                                // console($data);
                                $accordionId = time() . rand(9999, 1000); ?>
                                <div id="accordion_<?= $accordionId ?>" class="p-0 ">
                                    <?php foreach ($data as $key => $oneData) {
                                        $collapseId = $accordionId . "_" . $key;
                                        $itemIsGroup = isset($oneData["children_summary"]) ? true : false;
                                        $closingBalance = $itemIsGroup ? $oneData["children_summary"]["closing_val"] : $oneData["closing_val"]; 

                                        if($oneData["id"] == 2 || $oneData["id"] == 107){
                                            $closingBalance+=$openingBalAdjustmentVal;
                                        }

                                        if($isZeroValGlHide && $closingBalance==0){
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
                                                <?php } else if($oneData["id"]=="") { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><a><?= ($closingBalance < 0) ? "(" . number_format(abs($closingBalance), 2) . ")" : number_format($closingBalance, 2) ?></a></p>
                                                <?php } else { ?>
                                                    <p class="mr-2 text-sm font-weight-bold <?= $itemIsGroup ? "text-primary" : "text-dark" ?>"><a target="_blank" href="<?= LOCATION_URL ?>manage-daybook.php?mode=sablager&glId=<?= $oneData["id"]; ?>&to_date=<?= $balanceSheetReportDate ?>"><?= ($closingBalance < 0) ? "(" . number_format(abs($closingBalance), 2) . ")" : number_format($closingBalance, 2) ?></a></p>
                                                <?php } ?>
                                            </div>
                                            <div id="collapse_<?= $collapseId ?>" class="accordion-collapse collapse bg-light text-dark">
                                                <?php if ($itemIsGroup) { ?>
                                                    <div class="pt-2 pl-4">
                                                        <?php
                                                        if($oneData["id"] == 107){
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
                                                                        "closing_val" => $openingBalAdjustmentVal,
                                                                                
                                                                    ]
                                                                ],
                                                                "children_summary"=>["closing_val"=>$openingBalAdjustmentVal]
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