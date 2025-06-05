<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
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

<?php
class ComplianceGSTR1b2bData
{
    public $fyStartDate = "2022-04-01";
    public $fyEndDate = "2023-03-31";

    private function getOneGstinInvoiceItems($invoiceSummary)
    {
        $itemList = [];
        $invoiceId = $invoiceSummary["so_invoice_id"];
        $invoiceItemsObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE `so_invoice_id`=' . $invoiceId . ' AND `status`="active"', true);

        foreach ($invoiceItemsObj["data"] as $oneInvoiceItem) {
            $cgst = 0;
            $sgst = 0;
            $igst = 0;
            $cessAmount = 0;
            if ($invoiceSummary["igst"] > 0) {
                $igst = $oneInvoiceItem["totalTax"];
                $itemList[] = [
                    "num" => $oneInvoiceItem["lineNo"],
                    "itm_det" => [
                        "rt" => sprintf('%0.2f', round($oneInvoiceItem["unitPrice"], 2)),
                        "txval" => sprintf('%0.2f', round($oneInvoiceItem["totalPrice"], 2)),
                        "iamt" => sprintf('%0.2f', round($igst, 2)),
                        "csamt" => sprintf('%0.2f', round($cessAmount, 2))
                    ]
                ];
            } else {
                $cgst = $sgst = $oneInvoiceItem["totalTax"] / 2;
                $itemList[] = [
                    "num" => $oneInvoiceItem["lineNo"],
                    "itm_det" => [
                        "rt" => sprintf('%0.2f', round($oneInvoiceItem["unitPrice"], 2)),
                        "txval" => sprintf('%0.2f', round($oneInvoiceItem["totalPrice"], 2)),
                        "camt" => sprintf('%0.2f', round($cgst, 2)),
                        "samt" => sprintf('%0.2f', round($sgst, 2)),
                        "csamt" => sprintf('%0.2f', round($cessAmount, 2))
                    ]
                ];
            }
        }
        return $itemList;
    }
    private function getOneGstinInvoices($customerGstin)
    {
        global $branch_id;
        $invoiceSummaryObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $branch_id . ' AND `customer_gstin`="' . $customerGstin . '" AND `created_at` BETWEEN "' . $this->fyStartDate . '" AND "' . $this->fyEndDate . '"', true);

        $modifiedInvoiceList = [];
        foreach ($invoiceSummaryObj["data"] as $invoiceSummary) {
            // console($invoiceSummary);
            $modifiedInvoiceList[] = [
                "inum" => $invoiceSummary["invoice_no"],
                "idt" => date("d-m-Y", strtotime($invoiceSummary["created_at"])),
                "val" => sprintf('%0.2f', round($invoiceSummary["all_total_amt"], 2)),
                "pos" => substr($customerGstin, 0, 2),
                "rchrg" => "N/A",
                "etin" => $customerGstin,
                "inv_typ" => "R", //R:Regular B2B Invoices,  DE: Deemed Exports, SEWP: SEZ Exports with payment, SEWOP: SEZ exports without payment, CBW: Custom Bonded Warehouse
                "diff_percent" => 0,
                "itms" => $this->getOneGstinInvoiceItems($invoiceSummary)
            ];
        }
        return $modifiedInvoiceList;
    }

    function getGstr1b2bJsonData()
    {
        global $branch_id;
        $b2bDataList = [];
        $b2bInvoicesObj = queryGet('SELECT  DISTINCT(`customer_gstin`) AS customer_gstin FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $branch_id . ' AND `customer_gstin`!="" AND `created_at` BETWEEN "' . $this->fyStartDate . '" AND "' . $this->fyEndDate . '"', true);
        foreach ($b2bInvoicesObj["data"] as $oneB2bInvoicesGstin) {
            $customerGstin = $oneB2bInvoicesGstin["customer_gstin"];
            $b2bDataList[] = [
                "ctin" => $customerGstin,
                "inv" => $this->getOneGstinInvoices($customerGstin)
            ];
        }
        return $b2bDataList;
    }
}

class ComplianceGSTR1ViewData
{
    private $company_id = null;
    private $branch_id = null;
    private $fyStartDate = "";
    private $fyEndDate = "";
    private $b2bDataList = [];
    private $b2csDataList = [];
    private $b2clDataList = [];
    private $cdnrDataList = [];
    private $cdnurDataList = [];
    private $expDataList = [];
    private $atDataList = [];
    private $atadjDataList = [];
    private $exempDataList = [];
    private $hsnDataList = [];
    private $docsDataList = [];

    public $testData;

    function __construct($fyStartDate = "2022-04-01", $fyEndDate = "2023-03-31")
    {
        global $company_id, $branch_id;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;

        $this->fyStartDate = $fyStartDate;
        $this->fyEndDate = $fyEndDate;

        $sql = 'SELECT
            invoiceItems.so_invoice_item_id as invItemId,
            invoiceItems.lineNo as invItemLineNo,
            invoiceItems.inventory_item_id,
            invoiceItems.itemCode,
            invoiceItems.itemName,
            invoiceItems.uom as invItemUom,
            invoiceItems.hsnCode as invItemHsnCode,
            invoiceItems.qty as invItemQty,
            invoiceItems.unitPrice as invItemUnitPrice,
            invoiceItems.basePrice as invItemBasePrice,
            invoiceItems.tax as invItemTaxRate,
            invoiceItems.totalTax as invItemTotalTax,
            invoiceItems.totalDiscountAmt as invItemTotalDiscount,
            invoiceItems.totalPrice as invItemTotalPrice,
            invoices.so_invoice_id as invoiceId,
            invoices.invoice_no,
            invoices.invoice_date,
            invoices.totalItems as invoiceTotalItems,
            invoices.customer_id,
            invoices.customer_gstin,
            invoices.customerDetails,
            invoices.customer_billing_address,
            invoices.customer_shipping_address,
            invoices.igst as invoiceIgst,
            invoices.sgst as invoiceSgst,
            invoices.cgst as invoiceCsgt,
            invoices.totalDiscount as invoiceTotalDiscount,
            invoices.total_tax_amt as invoiceTotalTaxAmt,
            invoices.sub_total_amt as invoiceSubTotalAmt,
            invoices.all_total_amt as invoiceTotalAmt,
            invoices.company_id,
            invoices.companyDetails,
            invoices.company_gstin,
            invoices.`created_at` as invoiceCreatedAt,
            invoices.status as invoiceActiveFlag,
            invoices.invoiceStatus
            FROM
                `erp_branch_sales_order_invoice_items` AS invoiceItems,
                `erp_branch_sales_order_invoices` AS invoices
            WHERE
                invoiceItems.`so_invoice_id` = invoices.`so_invoice_id` AND invoices.`branch_id` = ' . $this->branch_id . ' AND invoices.`created_at` BETWEEN "' . $this->fyStartDate . '" AND "' . $this->fyEndDate . '"';


        $invoicesListObj = queryGet($sql, true);
        // console($invoicesListObj);
        foreach ($invoicesListObj["data"] as $key => $oneInvItem) {
            $company_gstin = $oneInvItem["company_gstin"];
            $customer_gstin = $oneInvItem["customer_gstin"];
            if ($oneInvItem["customer_gstin"] == "NA" || $oneInvItem["customer_gstin"] == "") {     // [B2C] checking b2C invoice
                if ($oneInvItem["invoiceTotalAmt"] > 200000) {                                      // [B2CL INVOICE] if invoice total amt is grater than 2.5 lakhs, invoice is b2cl
                    $this->b2clDataList[$oneInvItem['invoiceId']][intval($oneInvItem['invItemTaxRate'])][] = $oneInvItem;
                } else {                                                                            // [B2CS]  b2c invoice
                    $this->b2csDataList[$oneInvItem['invoiceId']][intval($oneInvItem['invItemTaxRate'])][] = $oneInvItem;
                }
            } else {                                                                                // [B2B] checking b2b invoice
                $this->b2bDataList[$oneInvItem['invoiceId']][intval($oneInvItem['invItemTaxRate'])][] = $oneInvItem;
            }

            // HSN Details
            if (($oneInvItem["customer_gstin"] == "NA" || $oneInvItem["customer_gstin"] == "")) {
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["hsn"] = ($oneInvItem['invItemHsnCode']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxableValue"] += ($oneInvItem['invItemTotalPrice']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxRate"] = ($oneInvItem['invItemTaxRate']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["igst"] += ($oneInvItem['invItemTaxRate']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["cgst"] = "";
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["sgst"] = "";
            } else {
                if (substr($company_gstin, 0, 2) == substr($customer_gstin, 0, 2)) {
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["hsn"] = ($oneInvItem['invItemHsnCode']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxableValue"] += ($oneInvItem['invItemTotalPrice']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxRate"] = ($oneInvItem['invItemTaxRate']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["cgst"] += ($oneInvItem['invItemTotalTax'] / 2);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["sgst"] += ($oneInvItem['invItemTotalTax'] / 2);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["igst"] = "";
                } else {
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["hsn"] = ($oneInvItem['invItemHsnCode']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxableValue"] += ($oneInvItem['invItemTotalPrice']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxRate"] = ($oneInvItem['invItemTaxRate']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["igst"] += ($oneInvItem['invItemTaxRate']);
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["cgst"] = "";
                    $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["sgst"] = "";
                }
            }
        }
    }

    function getb2bData()
    {
        $b2bDataList = $this->b2bDataList;

        if (count($b2bDataList) > 0) {
            return [
                "status" => "success",
                "message" => "B2b data fetched successfully",
                "data" => $b2bDataList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "B2b data not found",
                "data" => $b2bDataList
            ];
        }
    }

    function getb2csData()
    {
        $b2csDataList = $this->b2csDataList;
        if (count($b2csDataList) > 0) {
            return [
                "status" => "success",
                "message" => "B2CS data fetched successfully",
                "data" => $b2csDataList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "B2CS data not found",
                "data" => $b2csDataList
            ];
        }
    }

    function getb2clData()
    {
        $b2clDataList = $this->b2clDataList;
        if (count($b2clDataList) > 0) {
            return [
                "status" => "success",
                "message" => "B2CL data fetched successfully",
                "data" => $b2clDataList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "B2CL data not found",
                "data" => $b2clDataList
            ];
        }
    }

    function getHsnData()
    {
        $hsnDataList = $this->hsnDataList;
        if (count($hsnDataList) > 0) {
            return [
                "status" => "success",
                "message" => "HSN data fetched successfully",
                "data" => $hsnDataList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "HSN data not found",
                "data" => $hsnDataList
            ];
        }
    }
    function getSummaryData()
    {
        $b2bSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `customer_gstin`!="" AND `customer_gstin`!="NA"');
        $b2csSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND (`customer_gstin`="" OR `customer_gstin`="NA") AND `all_total_amt`<=200000');
        $b2clSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND (`customer_gstin`="" OR `customer_gstin`="NA") AND `all_total_amt`>200000');

        if ($b2bSumObj["status"] == "success" || $b2csSumObj["status"] == "success" || $b2clSumObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "Summary data fetched successfully",
                "data" => [
                    "B2B Invoices - 4A, 4B, 4C, 6B, 6C" => $b2bSumObj["data"],
                    "B2C(Small) Invoices - 7" => $b2csSumObj["data"],
                    "B2C(Large) Invoices - 5A, 5B" => $b2clSumObj["data"]
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Summary data not found",
                "data" => []
            ];
        }
    }
}

?>

<link rel="stylesheet" href="../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <?php
                $complianceGSTR1b2bDataObj = new ComplianceGSTR1b2bData();
                $complianceGSTR1b2bDataList = $complianceGSTR1b2bDataObj->getGstr1b2bJsonData();
                // console($complianceGSTR1b2bDataList);

                $complianceGSTR1ViewDataObj = new ComplianceGSTR1ViewData();
                $getb2bDataObj = $complianceGSTR1ViewDataObj->getb2bData();
                $getb2csDataObj = $complianceGSTR1ViewDataObj->getb2csData();
                $getb2clDataObj = $complianceGSTR1ViewDataObj->getb2clData();
                $getHsnDataObj = $complianceGSTR1ViewDataObj->getHsnData();
                $getSummaryDataObj = $complianceGSTR1ViewDataObj->getSummaryData();
                ?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body px-0 pb-0">

                            <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 5px;">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#gstr1ReviewTabDiv" role="tab" aria-selected="true">Review</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#gstr1ActionTabDiv" role="tab" aria-selected="true">Action</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="gstr1ReviewTabDiv" role="tabpanel" aria-labelledby="listTab">
                                    <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 5px;">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#gstr1MgtReportTabDiv" role="tab" aria-selected="true">Mgt Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#gstr1StrtReportTabDiv" role="tab" aria-selected="true">Strt Report</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="gstr1MgtReportTabDiv" role="tabpanel" aria-labelledby="listTab">
                                            <p>gstr1MgtReportTabDiv</p>
                                        </div>
                                        <div class="tab-pane fade" id="gstr1StrtReportTabDiv" role="tabpanel" aria-labelledby="listTab">
                                            <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 5px;">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#gstr1SummaryTabDiv" role="tab" aria-selected="true">Summary</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#gstr1DetailsTabDiv" role="tab" aria-selected="true">Details</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
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
                                                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="background-color: #001621;padding: 5px;">
                                                        <!-- <li class="nav-item complince"> -->
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-toggle="tab" href="#gstr1b2bTabDiv" role="tab" aria-selected="true">b2b</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1b2clTabDiv" role="tab" aria-selected="true">b2cl</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1b2csTabDiv" role="tab" aria-selected="true">b2cs</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1cdnrTabDiv" role="tab" aria-selected="true">cdnr</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1cdnurTabDiv" role="tab" aria-selected="true">cdnur</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1expTabDiv" role="tab" aria-selected="true">exp</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1atTabDiv" role="tab" aria-selected="true">at</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1atadjTabDiv" role="tab" aria-selected="true">atadj</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1exempTabDiv" role="tab" aria-selected="true">exemp</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1hsnTabDiv" role="tab" aria-selected="true">hsn</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#gstr1docsTabDiv" role="tab" aria-selected="true">docs</a>
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
                                                                            <th>Total Value</th>
                                                                            <th>Rate</th>
                                                                            <th>Taxable Value</th>
                                                                            <th>Integrated Tax Amount</th>
                                                                            <th>Central Tax Amount</th>
                                                                            <th>State/UT Tax Amount</th>
                                                                            <th>Cess Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        foreach ($getHsnDataObj["data"] as $oneHsnDetails) {
                                                                            foreach ($oneHsnDetails as $oneRow) {
                                                                        ?>
                                                                                <tr>
                                                                                    <td><?= $oneRow["hsn"] ?></td>
                                                                                    <td><?= $oneRow["hsnDescription"] ?? "" ?></td>
                                                                                    <td><?= $oneRow["UQC"] ?? "NA" ?></td>
                                                                                    <td><?= $oneRow["totalQuantity"] ?? "" ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["totalValue"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["taxRate"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["taxableValue"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["igst"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["cgst"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["sgst"], 2) ?></td>
                                                                                    <td class="text-right"><?= number_format($oneRow["cess"] ?? "", 2) ?></td>
                                                                                </tr>
                                                                        <?php
                                                                            }
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
                                <div class="tab-pane fade" id="gstr1ActionTabDiv" role="tabpanel" aria-labelledby="listTab" style="overflow-x: auto;">
                                    <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 5px;">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#gstr1JsonDataTabDiv" role="tab" aria-selected="true">JSON Data</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#gstr1FileTabDiv" role="tab" aria-selected="true">FILE GSTR-1</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="gstr1JsonDataTabDiv" role="tabpanel" aria-labelledby="listTab">
                                            <p>gstr1JsonDataTabDiv</p>
                                        </div>
                                        <div class="tab-pane fade" id="gstr1FileTabDiv" role="tabpanel" aria-labelledby="listTab">
                                            <p id="timerTxtId" class="text-center h3 font-weight-bold"></p>
                                            <script>
                                                var countDownDate = new Date("Mar 11, 2023 00:00:00").getTime();
                                                var countDownObj = setInterval(function() {
                                                    var now = new Date().getTime();
                                                    var distance = countDownDate - now;

                                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                    var timerTxt = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

                                                    document.getElementById("timerTxtId").innerHTML = `${timerTxt} To go!`;

                                                    if (distance < 0) {
                                                        clearInterval(countDownObj);
                                                        document.getElementById("demo").innerHTML = "Click here to File!";
                                                    }
                                                }, 1000);
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                // console($getb2csDataObj);
                // console($getb2clDataObj);
                // console($getb2bDataObj);
                ?>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("common/footer.php");
?>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="../public/assets/piechart/piecore.js"></script>
<script src="//www.amcharts.com/lib/4/charts.js"></script>
<script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<script>
    $(function() {
        $("#gstr1b2bTable_wrapper").DataTable({
            "responsive": true,
            "lengthChange": false,
            paging: false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#defaultDataTable_wrapper .col-md-6:eq(0)');

    });
</script>