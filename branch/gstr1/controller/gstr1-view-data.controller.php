<?php
class ComplianceGSTR1ViewData
{
    private $company_id = null;
    private $branch_id = null;
    private $branch_gstin;
    private $branch_gstin_code;
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

    function __construct($fyStartDate = null, $fyEndDate = null)
    {
        global $company_id;
        global $branch_id;
        global $branch_gstin;

        global $location_id;
        global $created_by;
        global $updated_by;

        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->branch_gstin = $branch_gstin;
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);

        $this->fyStartDate = $fyStartDate != null ? $fyStartDate : date("Y-m-d", strtotime('first day of last month'));
        $this->fyEndDate = $fyEndDate != null ? $fyEndDate : date("Y-m-d", strtotime('last day of last month'));

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
            (invoiceItems.basePrice-invoiceItems.totalDiscountAmt) as invItemSubTotalAmt,
            invoices.so_invoice_id as invoiceId,
            invoices.invoice_no,
            invoices.invoice_date,
            invoices.totalItems as invoiceTotalItems,
            invoices.customer_id,
            invoices.customer_gstin,
            invoices.placeOfSupply,
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
                invoiceItems.`so_invoice_id` = invoices.`so_invoice_id` AND invoices.`branch_id` = ' . $this->branch_id . ' AND invoices.`invoice_date` BETWEEN "' . $this->fyStartDate . '" AND "' . $this->fyEndDate . '"';


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
            if (substr($company_gstin, 0, 2) == $oneInvItem["placeOfSupply"]) {
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["hsn"] = ($oneInvItem['invItemHsnCode']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxableValue"] += ($oneInvItem['invItemSubTotalAmt']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxRate"] = ($oneInvItem['invItemTaxRate']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["cgst"] += ($oneInvItem['invItemTotalTax'] / 2);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["sgst"] += ($oneInvItem['invItemTotalTax'] / 2);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["igst"] = "";
            } else {
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["hsn"] = ($oneInvItem['invItemHsnCode']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxableValue"] += ($oneInvItem['invItemSubTotalAmt']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxRate"] = ($oneInvItem['invItemTaxRate']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["igst"] += ($oneInvItem['invItemTotalTax']);
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["cgst"] = "";
                $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["sgst"] = "";
            }
            // if (($oneInvItem["customer_gstin"] == "NA" || $oneInvItem["customer_gstin"] == "")) {
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["hsn"] = ($oneInvItem['invItemHsnCode']);
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxableValue"] += ($oneInvItem['invItemSubTotalAmt']);
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxRate"] = ($oneInvItem['invItemTaxRate']);
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["igst"] += ($oneInvItem['invItemTotalTax']);
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["cgst"] = "";
            //     $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["sgst"] = "";
            // } else {
            //     if (substr($company_gstin, 0, 2) == substr($customer_gstin, 0, 2)) {
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["hsn"] = ($oneInvItem['invItemHsnCode']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxableValue"] += ($oneInvItem['invItemSubTotalAmt']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["taxRate"] = ($oneInvItem['invItemTaxRate']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["cgst"] += ($oneInvItem['invItemTotalTax'] / 2);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["sgst"] += ($oneInvItem['invItemTotalTax'] / 2);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][0]["igst"] = "";
            //     } else {
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["hsn"] = ($oneInvItem['invItemHsnCode']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["totalValue"] += ($oneInvItem['invItemTotalPrice']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxableValue"] += ($oneInvItem['invItemSubTotalAmt']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["taxRate"] = ($oneInvItem['invItemTaxRate']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["igst"] += ($oneInvItem['invItemTotalTax']);
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["cgst"] = "";
            //         $this->hsnDataList[$oneInvItem['invItemHsnCode']][1]["sgst"] = "";
            //     }
            // }
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
        $tempData = [];
        foreach ($this->hsnDataList as $hsnCode => $hsnDataList) {
            foreach ($hsnDataList as $hsnData) {
                if (isset($tempData[$hsnCode][$hsnData["taxRate"]])) {
                    $tempData[$hsnCode][$hsnData["taxRate"]]["totalValue"] += $hsnData["totalValue"];
                    $tempData[$hsnCode][$hsnData["taxRate"]]["taxableValue"] += $hsnData["taxableValue"];
                    $tempData[$hsnCode][$hsnData["taxRate"]]["cgst"] += $hsnData["cgst"] > 0 ? $hsnData["cgst"] : 0;
                    $tempData[$hsnCode][$hsnData["taxRate"]]["sgst"] += $hsnData["sgst"] > 0 ? $hsnData["sgst"] : 0;
                    $tempData[$hsnCode][$hsnData["taxRate"]]["igst"] += $hsnData["igst"] > 0 ? $hsnData["igst"] : 0;
                } else {
                    $tempData[$hsnCode][$hsnData["taxRate"]] = $hsnData;
                }
            }
        }

        $finalResult = [];
        foreach ($tempData as $hsnDataRateWiseList) {
            foreach ($hsnDataRateWiseList as $hsnDataRow) {
                $finalResult[] = $hsnDataRow;
            }
        }

        if (count($finalResult) > 0) {
            return [
                "status" => "success",
                "message" => "HSN data fetched successfully",
                "data" => $finalResult,
                // "original"=>$this->hsnDataList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "HSN data not found",
                "data" => $finalResult
            ];
        }
    }


    function getCreditDebitNotes()
    {
        // data from creditnote table
        $creditNoteObj = queryGet("SELECT * FROM `erp_credit_note` WHERE `branch_id`=$this->branch_id AND `status`='active' AND `postingDate` BETWEEN '$this->fyStartDate' AND '$this->fyEndDate'", true);

        $tempRegSummary = [];
        $tempRegData = [];
        $tempUnregSummary = [];
        $tempUnregData = [];

        foreach ($creditNoteObj["data"] as $crDrNote) {
            $crDrNoteId = $crDrNote["cr_note_id"];
            $crDrNoteItemObj = queryGet("SELECT * FROM `credit_note_item` WHERE `credit_note_id`=$crDrNoteId", true);
            $partyId = $crDrNote["party_id"];
            $partyCode = $crDrNote["party_code"];
            $partyGstin = "";
            $partyPan = "";
            if ($crDrNote["creditors_type"] == "vendor") {
                $vendorObj = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $vendorObj["data"]["vendor_gstin"] ?? "";
                $partyPan = $vendorObj["data"]["vendor_pan"] ?? "";
            } else {
                $customerObj = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $customerObj["data"]["customer_gstin"] ?? "";
                $partyPan = $customerObj["data"]["customer_pan"] ?? "";
            }

            $tempNote = [
                "partyGstin" => $partyGstin,
                "partyCode" => $partyCode,
                "partyPan" => $partyPan,
                "ntty" => ($crDrNote["creditors_type"] == "vendor") ? "C" : "D",
                "nt_num" => $crDrNote["credit_note_no"],
                "nt_dt" => $crDrNote["postingDate"],
                "p_gst" => "N",
                "pos" => $crDrNote["destination_address"] > 9 ? $crDrNote["destination_address"] : "0" . $crDrNote["destination_address"],
                "rchrg" => "N",
                "inv_typ" => "R",
                "val" => $crDrNote["total"],
                "diff_percent" => $crDrNote["adjustment"],
                "totalTaxableAmt" => 0,
                "totalCgst" => 0,
                "totalSgst" => 0,
                "totalIgst" => 0,
                "totalCess" => 0
            ];

            foreach ($crDrNoteItemObj["data"] as $oneItemKey => $oneItem) {
                $tempItem["num"] = $oneItemKey + 1;
                $tempItem["itm_det"]["rt"] = $oneItem["item_tax"];
                $tempItem["itm_det"]["txval"] = $oneItem["item_qty"] * $oneItem["item_rate"];
                $tempNote["totalTaxableAmt"]+= $tempItem["itm_det"]["txval"];
                if ($partyGstin == "" || $this->branch_gstin_code == $crDrNote["destination_address"]) {
                    $tempNote["totalSgst"] = $tempNote["totalCgst"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]) / 2;
                } else {
                    $tempNote["totalIgst"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]);
                }
                $tempNote["totalCess"] = 0;
            }


            if ($partyGstin != "") {
                $tempRegData[] = $tempNote;
                $tempRegSummary["voucherCount"]+=1;
                $tempRegSummary["totalCgst"]+=$tempNote["totalCgst"];
                $tempRegSummary["totalSgst"]+=$tempNote["totalSgst"];
                $tempRegSummary["totalIgst"]+=$tempNote["totalIgst"];
                $tempRegSummary["totalCess"]+=$tempNote["totalCess"];
                $tempRegSummary["taxableAmount"]+= $tempNote["totalTaxableAmt"];
            } else {
                $tempUnregSummary["voucherCount"]+=1;
                $tempUnregSummary["totalCgst"]+=$tempNote["totalCgst"];
                $tempUnregSummary["totalSgst"]+=$tempNote["totalSgst"];
                $tempUnregSummary["totalIgst"]+=$tempNote["totalIgst"];
                $tempUnregSummary["totalCess"]+=$tempNote["totalCess"];
                $tempUnregSummary["taxableAmount"]+= $tempNote["totalTaxableAmt"];
                unset($tempNote["rchrg"]);
                unset($tempNote["inv_typ"]);
                $tempNote["typ"] = "B2CL";
                $tempUnregData[] = $tempNote;
            }
        }




        // data from debit table
        $debitNoteObj = queryGet("SELECT * FROM `erp_debit_note` WHERE `branch_id`=$this->branch_id AND `status`='active' AND `postingDate` BETWEEN '$this->fyStartDate' AND '$this->fyEndDate'", true);

        foreach ($debitNoteObj["data"] as $crDrNote) {
            $crDrNoteId = $crDrNote["dr_note_id"];
            $crDrNoteItemObj = queryGet("SELECT * FROM `debit_note_item` WHERE `debit_note_id`=$crDrNoteId", true);
            $partyId = $crDrNote["party_id"];
            $partyCode = $crDrNote["party_code"];
            $partyGstin = "";
            $partyPan = "";
            if ($crDrNote["debitor_type"] == "vendor") {
                $vendorObj = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $vendorObj["data"]["vendor_gstin"] ?? "";
                $partyPan = $vendorObj["data"]["vendor_pan"] ?? "";
            } else {
                $customerObj = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $customerObj["data"]["customer_gstin"] ?? "";
                $partyPan = $customerObj["data"]["customer_pan"] ?? "";
            }
            $tempNote = [
                "partyGstin" => $partyGstin,
                "partyCode" => $partyCode,
                "partyPan" => $partyPan,
                "ntty" => ($crDrNote["debitor_type"] == "vendor") ? "D" : "C",
                "nt_num" => $crDrNote["debit_note_no"],
                "nt_dt" => $crDrNote["postingDate"],
                "p_gst" => "N",
                "pos" => $crDrNote["destination_address"] > 9 ? $crDrNote["destination_address"] : "0" . $crDrNote["destination_address"],
                "rchrg" => "N",
                "inv_typ" => "R",
                "val" => $crDrNote["total"],
                "diff_percent" => $crDrNote["adjustment"],
                "itms" => []
            ];

            foreach ($crDrNoteItemObj["data"] as $oneItemKey => $oneItem) {
                $tempItem["num"] = $oneItemKey + 1;
                $tempItem["itm_det"]["rt"] = $oneItem["item_tax"];
                $tempItem["itm_det"]["txval"] = $oneItem["item_qty"] * $oneItem["item_rate"];
                $tempNote["totalTaxableAmt"]+= $tempItem["itm_det"]["txval"];
                if ($partyGstin == "" || $this->branch_gstin_code == $crDrNote["destination_address"]) {
                    $tempNote["totalSgst"] = $tempNote["totalCgst"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]) / 2;
                } else {
                    $tempNote["totalIgst"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]);
                }
                $tempNote["totalCess"] = 0;
            }

            if ($partyGstin != "") {
                $tempRegSummary["voucherCount"]+=1;
                $tempRegSummary["totalCgst"]+=$tempNote["totalCgst"];
                $tempRegSummary["totalSgst"]+=$tempNote["totalSgst"];
                $tempRegSummary["totalIgst"]+=$tempNote["totalIgst"];
                $tempRegSummary["totalCess"]+=$tempNote["totalCess"];
                $tempRegSummary["taxableAmount"]+= $tempNote["totalTaxableAmt"];
                $tempRegData[] = $tempNote;
            } else {
                $tempUnregSummary["voucherCount"]+=1;
                $tempUnregSummary["totalCgst"]+=$tempNote["totalCgst"];
                $tempUnregSummary["totalSgst"]+=$tempNote["totalSgst"];
                $tempUnregSummary["totalIgst"]+=$tempNote["totalIgst"];
                $tempUnregSummary["totalCess"]+=$tempNote["totalCess"];
                $tempUnregSummary["taxableAmount"]+= $tempNote["totalTaxableAmt"];
                unset($tempNote["rchrg"]);
                unset($tempNote["inv_typ"]);
                $tempNote["typ"] = "B2CL";
                $tempUnregData[] = $tempNote;
            }
        }

        // generate summary
        $tempRegSummary["totalTax"] = $tempRegSummary["totalCgst"] + $tempRegSummary["totalSgst"] + $tempRegSummary["totalIgst"];
        $tempUnregSummary["totalTax"] = $tempUnregSummary["totalCgst"] + $tempUnregSummary["totalSgst"] + $tempUnregSummary["totalIgst"];
        return [
            "cdnr" => $tempRegData,
            "cdnrSummary" => $tempRegSummary,
            "cdnur" => $tempUnregData,
            "cdnurSummary" => $tempUnregSummary
        ];
    }



    function getSummaryData()
    {
        $b2bSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `customer_gstin`!="" AND `customer_gstin`!="NA"');
        $b2csSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND (`customer_gstin`="" OR `customer_gstin`="NA") AND `all_total_amt`<=200000');
        $b2clSumObj = queryGet('SELECT COUNT(`so_invoice_id`) as voucherCount, SUM(`sub_total_amt`) AS taxableAmount, SUM(`cgst`) AS totalCgst, SUM(`sgst`) AS totalSgst, SUM(`igst`) AS totalIgst, "" AS totalCess, SUM(`total_tax_amt`) AS totalTax, SUM(`all_total_amt`) AS totalInvAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND (`customer_gstin`="" OR `customer_gstin`="NA") AND `all_total_amt`>200000');


        $crDrNotesObj = $this->getCreditDebitNotes();

        if ($b2bSumObj["status"] == "success" || $b2csSumObj["status"] == "success" || $b2clSumObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "Summary data fetched successfully",
                "data" => [
                    "B2B Invoices - 4A, 4B, 4C, 6B, 6C" => $b2bSumObj["data"],
                    "B2C(Small) Invoices - 7" => $b2csSumObj["data"],
                    "B2C(Large) Invoices - 5A, 5B" => $b2clSumObj["data"],
                    "Credit/Debit Notes (Registered) - 9B" => $crDrNotesObj["cdnrSummary"],
                    "Credit/Debit Notes(Unregistered) - 9B" => $crDrNotesObj["cdnurSummary"],
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
