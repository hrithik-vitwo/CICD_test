<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("controller/gstr1-view-data.controller.php");
require_once("controller/gstr1-json-data.controller.php");
// administratorAuth();
?>
<style>
    .filter-list a {
        background: #fff;
        box-shadow: 1px 2px 5px -1px #8e8e8e;
    }

    .filter-list {
        margin-bottom: 2em;
    }

    li.nav-item.complince a {
        background: #fff;
        color: #003060;
        z-index: 9;
        margin-bottom: 1em;
    }
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <?php
                $fromDate = (isset($_GET["fromDate"]) && $_GET["fromDate"] != "") ? $_GET["fromDate"] : date("Y-m-d", strtotime('first day of last month'));
                $toDate = (isset($_GET["toDate"]) && $_GET["toDate"] != "") ? $_GET["toDate"] : date("Y-m-d", strtotime('last day of last month'));

                $complianceGSTR1ViewDataObj = new ComplianceGSTR1ViewData($fromDate, $toDate);
                $getb2bDataObj = $complianceGSTR1ViewDataObj->getb2bData();

                // console($getb2bDataObj);

                $getb2csDataObj = $complianceGSTR1ViewDataObj->getb2csData();
                $getb2clDataObj = $complianceGSTR1ViewDataObj->getb2clData();
                $getHsnDataObj = $complianceGSTR1ViewDataObj->getHsnData();
                $getSummaryDataObj = $complianceGSTR1ViewDataObj->getSummaryData();

                // console($getb2csDataObj);
                // console($getHsnDataObj);
                ?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body px-0 pb-3">
                            <div class="row p-0 m-0 px-2">
                                <div class="col-md-4 d-flex gap-2 text-nowrap pl-0">
                                    <label>Select FY</label>
                                    <input type="date" name="fromDate" value="<?= $fromDate ?>" id="fyFromDate" class="fyDate form-control"><label>to</label>
                                    <input type="date" name="toDate" value="<?= $toDate ?>" id="fyToDate" class="fyDate form-control">
                                    <script>
                                        $(document).ready(function() {
                                            $('.fyDate').change(function() {
                                                window.location.href = `?fromDate=${$("#fyFromDate").val()}&toDate=${$("#fyToDate").val()}`;
                                            });
                                        });
                                    </script>
                                </div>
                                <div class="btn-group col-md-4 p-0 pb-1 ml-auto" role="group">
                                    <a href="gstr1-review.php" type="button" class="btn btn-secondary active">Review</a>
                                    <a href="gstr1-action.php" type="button" class="btn btn-secondary">Action</a>
                                </div>
                            </div>
                            <div class="row p-2 m-0">
                                <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 2px;">
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-xs active" data-toggle="tab" href="#gstr1MgtReportTabDiv" role="tab" aria-selected="true">Mgt Report</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1StrtReportTabDiv" role="tab" aria-selected="true">Strt Report</a>
                                    </li>
                                </ul>
                                <div class="tab-content p-1">
                                    <div class="tab-pane fade show active" id="gstr1MgtReportTabDiv" role="tabpanel" aria-labelledby="listTab">
                                        <div class="row p-0 m-0">
                                            MgtReport
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="gstr1StrtReportTabDiv" role="tabpanel" aria-labelledby="listTab">
                                        <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 2px;">
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-xs active" data-toggle="tab" href="#gstr1SummaryTabDiv" role="tab" aria-selected="true">Summary</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1DetailsTabDiv" role="tab" aria-selected="true">Details</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content p-1">
                                            <div class="tab-pane fade show active" id="gstr1SummaryTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                <table class="table defaultDataTable table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl</th>
                                                            <th>Partculars</th>
                                                            <th>Voucher Count</th>
                                                            <th>Taxable Amount</th>
                                                            <th>CGST</th>
                                                            <th>SGST</th>
                                                            <th>IGST</th>
                                                            <th>CESS</th>
                                                            <th>Total Tax</th>
                                                            <th>Invoice Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sl = 0;
                                                        $grandVoucherCount = 0;
                                                        $grandTaxableAmount = 0;
                                                        $grandTotalCgst = 0;
                                                        $grandTotalSgst = 0;
                                                        $grandTotalIgst = 0;
                                                        $grandTotalTax = 0;
                                                        $grandTotalCess = 0;
                                                        $grandTotalInvAmount = 0;
                                                        foreach ($getSummaryDataObj["data"] as $particular => $oneData) {
                                                            $grandVoucherCount += $oneData["voucherCount"];
                                                            $grandTaxableAmount += $oneData["taxableAmount"];
                                                            $grandTotalCgst += $oneData["totalCgst"];
                                                            $grandTotalSgst += $oneData["totalSgst"];
                                                            $grandTotalIgst += $oneData["totalIgst"];
                                                            $grandTotalTax += $oneData["totalTax"];
                                                            $grandTotalCess += $oneData["totalCess"];
                                                            $grandTotalInvAmount += $oneData["totalInvAmount"];
                                                        ?>
                                                            <tr>
                                                                <td><?= $sl += 1 ?></td>
                                                                <td><?= $particular ?></td>
                                                                <td><?= $oneData["voucherCount"] ?></td>
                                                                <td class="text-right"><?= number_format($oneData["taxableAmount"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalCgst"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalSgst"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalIgst"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalCess"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalTax"], 2) ?></td>
                                                                <td class="text-right"><?= number_format($oneData["totalInvAmount"], 2) ?></td>
                                                            </tr>

                                                        <?php
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td colspan="2" class="text-right font-weight-bold">Total</td>
                                                            <td class="font-weight-bold"><?= $grandVoucherCount ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTaxableAmount, 2) ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalCgst, 2) ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalSgst, 2) ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalIgst, 2) ?></td>
                                                            <td class="text-right font-weight-bold"><?= $grandTotalCess > 0 ? number_format($grandTotalCess, 2) : "" ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalTax, 2) ?></td>
                                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalInvAmount, 2) ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="gstr1DetailsTabDiv" role="tabpanel" aria-labelledby="listTab">
                                                <ul class="nav nav-tabs" id="myTab" role="tablist" style="background-color: #001621;padding: 2px;">
                                                    <!-- <li class="nav-item complince"> -->
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs active" data-toggle="tab" href="#gstr1b2bTabDiv" role="tab" aria-selected="true">b2b</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1b2clTabDiv" role="tab" aria-selected="true">b2cl</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1b2csTabDiv" role="tab" aria-selected="true">b2cs</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1cdnrTabDiv" role="tab" aria-selected="true">cdnr</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1cdnurTabDiv" role="tab" aria-selected="true">cdnur</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1expTabDiv" role="tab" aria-selected="true">exp</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1atTabDiv" role="tab" aria-selected="true">at</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1atadjTabDiv" role="tab" aria-selected="true">atadj</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1exempTabDiv" role="tab" aria-selected="true">exemp</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1hsnTabDiv" role="tab" aria-selected="true">hsn</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1docsTabDiv" role="tab" aria-selected="true">docs</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                                    <div class="tab-pane fade show active" id="gstr1b2bTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">

                                                        <table id="gstr1b2bTable" class="table table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>GSTIN/UIN of Recipient</th>
                                                                    <th>Receiver Name</th>
                                                                    <th>Invoice Number</th>
                                                                    <th>Invoice date</th>
                                                                    <th>Invoice Value</th>
                                                                    <th>Place Of Supply</th>
                                                                    <th>Reverse Charge</th>
                                                                    <th>Applicable % of Tax Rate</th>
                                                                    <th>Invoice Type</th>
                                                                    <th>E-Commerce GSTIN</th>
                                                                    <th>Rate</th>
                                                                    <th>Taxable Value</th>
                                                                    <th>Cess Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($getb2bDataObj["data"] as $invoiceItems) {
                                                                    foreach ($invoiceItems as $rate => $rateWiseItem) {
                                                                        $oneInvAndItem = $rateWiseItem[0];
                                                                        $rateWiseTaxableVal = 0;
                                                                        foreach ($rateWiseItem as $oneItem) {
                                                                            // console($oneItem);
                                                                            $rateWiseTaxableVal += $oneItem["invItemTotalPrice"] - $oneItem["invItemTotalTax"];
                                                                        }
                                                                ?>
                                                                        <tr>
                                                                            <td><?= $oneInvAndItem["customer_gstin"] ?></td>
                                                                            <td></td>
                                                                            <td><?= $oneInvAndItem["invoice_no"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoice_date"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoiceTotalAmt"] ?></td>
                                                                            <td><?= substr($oneInvAndItem["customer_gstin"], 0, 2) ?></td>
                                                                            <td>N/A</td>
                                                                            <td></td>
                                                                            <td>Regular</td>
                                                                            <td></td>
                                                                            <td><?= $oneInvAndItem["invItemTaxRate"] ?></td>
                                                                            <td><?= $rateWiseTaxableVal ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>

                                                    </div>

                                                    <div class="tab-pane fade" id="gstr1b2clTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        <table class="table defaultDataTable table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Invoice Number</th>
                                                                    <th>Invoice date</th>
                                                                    <th>Invoice Value</th>
                                                                    <th>Place Of Supply</th>
                                                                    <th>Reverse Charge</th>
                                                                    <th>Applicable % of Tax Rate</th>
                                                                    <th>Invoice Type</th>
                                                                    <th>E-Commerce GSTIN</th>
                                                                    <th>Rate</th>
                                                                    <th>Taxable Value</th>
                                                                    <th>Cess Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($getb2clDataObj["data"] as $invoiceItems) {
                                                                    foreach ($invoiceItems as $rate => $rateWiseItem) {
                                                                        $oneInvAndItem = $rateWiseItem[0];
                                                                        $rateWiseTaxableVal = 0;
                                                                        foreach ($rateWiseItem as $oneItem) {
                                                                            // console($oneItem);
                                                                            $rateWiseTaxableVal += $oneItem["invItemTotalPrice"] - $oneItem["invItemTotalTax"];
                                                                        }
                                                                ?>
                                                                        <tr>
                                                                            <td><?= $oneInvAndItem["invoice_no"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoice_date"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoiceTotalAmt"] ?></td>
                                                                            <td></td>
                                                                            <td>N/A</td>
                                                                            <td></td>
                                                                            <td>Regular</td>
                                                                            <td></td>
                                                                            <td><?= $oneInvAndItem["invItemTaxRate"] ?></td>
                                                                            <td><?= $rateWiseTaxableVal ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1b2csTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        <table class="table defaultDataTable table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Invoice Number</th>
                                                                    <th>Invoice date</th>
                                                                    <th>Invoice Value</th>
                                                                    <th>Place Of Supply</th>
                                                                    <th>Reverse Charge</th>
                                                                    <th>Applicable % of Tax Rate</th>
                                                                    <th>Invoice Type</th>
                                                                    <th>E-Commerce GSTIN</th>
                                                                    <th>Rate</th>
                                                                    <th>Taxable Value</th>
                                                                    <th>Cess Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($getb2csDataObj["data"] as $invoiceItems) {
                                                                    foreach ($invoiceItems as $rate => $rateWiseItem) {
                                                                        $oneInvAndItem = $rateWiseItem[0];
                                                                        $rateWiseTaxableVal = 0;
                                                                        foreach ($rateWiseItem as $oneItem) {
                                                                            // console($oneItem);
                                                                            $rateWiseTaxableVal += $oneItem["invItemTotalPrice"] - $oneItem["invItemTotalTax"];
                                                                        }
                                                                ?>
                                                                        <tr>
                                                                            <td><?= $oneInvAndItem["invoice_no"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoice_date"] ?></td>
                                                                            <td><?= $oneInvAndItem["invoiceTotalAmt"] ?></td>
                                                                            <td></td>
                                                                            <td>N/A</td>
                                                                            <td></td>
                                                                            <td>Regular</td>
                                                                            <td></td>
                                                                            <td><?= $oneInvAndItem["invItemTaxRate"] ?></td>
                                                                            <td><?= $rateWiseTaxableVal ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1cdnrTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        cdnur
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1cdnurTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        cdnur
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1expTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        exp
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1atTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        at
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1atadjTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        atadj
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1exempTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        exemp
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1hsnTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                                        <?php
                                                        if ($getHsnDataObj["status"] == "success") {
                                                            // console($getHsnDataObj);
                                                        ?>
                                                            <table class="table defaultDataTable table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>HSN</th>
                                                                        <th>Description</th>
                                                                        <th>UQC</th>
                                                                        <th>Total Quantity</th>
                                                                        <th>Taxable Value</th>
                                                                        <th>Rate</th>
                                                                        <th>Total Value</th>
                                                                        <th>Integrated Tax Amount</th>
                                                                        <th>Central Tax Amount</th>
                                                                        <th>State/UT Tax Amount</th>
                                                                        <th>Cess Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($getHsnDataObj["data"] as $oneRow) {
                                                                    ?>
                                                                        <tr>
                                                                            <td><?= $oneRow["hsn"] ?></td>
                                                                            <td><?= $oneRow["hsnDescription"] ?? "" ?></td>
                                                                            <td><?= $oneRow["UQC"] ?? "NA" ?></td>
                                                                            <td><?= $oneRow["totalQuantity"] ?? "" ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["taxableValue"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["taxRate"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["totalValue"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["igst"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["cgst"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["sgst"], 2) ?></td>
                                                                            <td class="text-right"><?= number_format($oneRow["cess"] ?? "", 2) ?></td>
                                                                        </tr>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <p class="text-center"><?= $getHsnDataObj["message"] ?></p>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="tab-pane fade" id="gstr1docsTabDiv" role="tabpanel" aria-labelledby="listTab">
                                                        docs
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
require_once("../common/footer.php");
?>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="../../public/assets/piechart/piecore.js"></script>
<script src="https://amcharts.com/lib/4/charts.js"></script>
<script src="https://amcharts.com/lib/4/themes/animated.js"></script>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://amcharts.com/lib/3/serial.js?x"></script>
<script src="https://amcharts.com/lib/3/themes/dark.js"></script>
<script>
    $(document).ready(function() {
        console.log("Document loaded");
    });
</script>