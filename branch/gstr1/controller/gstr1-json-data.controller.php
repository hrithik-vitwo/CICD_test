<?php

class ComplianceGstr1Json
{
    private $periodGstr1 = null;
    private $periodStart = null;
    private $periodEnd = null;
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $branch_gstin;
    private $branch_gstin_code;

    function __construct($periodGstr1 = null, $periodStart = null, $periodEnd = null)
    {
        $this->periodGstr1 = $periodGstr1;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;

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
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);
    }

    private function getB2b()
    {

        $b2bArr = [];

        $gstListObj = queryGet('SELECT DISTINCT(`customer_gstin`) AS customer_gstin FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `customer_gstin`!="" AND `invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '"', true);
        foreach ($gstListObj["data"] as $oneB2bInvoicesGstin) {
            $customerGstin = $oneB2bInvoicesGstin["customer_gstin"];
            // console($customerGstin);

            $custArr = [
                "ctin" => $customerGstin,
                "inv" => []
            ];

            $customerWiseInvoicesObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `customer_gstin`="' . $customerGstin . '" AND `invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '"', true);
            foreach ($customerWiseInvoicesObj["data"] as $oneInvoice) {
                // console($invoiceSummary);
                // $invoice_no = str_replace("/", "-", $oneInvoice["invoice_no"]);
                $invArr = [
                    "inum" => $oneInvoice["invoice_no"],
                    "idt" => date("d-m-Y", strtotime($oneInvoice["invoice_date"])),
                    "val" => floatval(round($oneInvoice["all_total_amt"], 2)),
                    "pos" => $oneInvoice["placeOfSupply"],
                    "rchrg" => $oneInvoice["reverseCharge"],
                    "inv_typ" => $oneInvoice["compInvoiceType"],
                    "itms" => []
                ];

                $invoiceItemsObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE `so_invoice_id`=' . $oneInvoice["so_invoice_id"] . ' AND `status`="active"', true);
                foreach ($invoiceItemsObj["data"] as $key => $oneItem) {
                    $taxableAmount = $oneItem["basePrice"] - $oneItem["totalDiscountAmt"];
                    $cgst = ($taxableAmount * $oneItem["tax"] / 100) / 2;
                    $sgst = ($taxableAmount * $oneItem["tax"] / 100) / 2;
                    $igst = ($taxableAmount * $oneItem["tax"] / 100);
                    $cessAmount = 0;
                    if ($oneInvoice["igst"] > 0) {
                        $invArr["itms"][] = [
                            "num" => $key + 1,
                            "itm_det" => [
                                "txval" => floatval(round($taxableAmount, 2)),
                                "rt" => floatval(round($oneItem["tax"], 2)),
                                "iamt" => floatval(round($igst, 2)),
                                "csamt" => floatval(round($cessAmount, 2))
                            ]
                        ];
                    } else {
                        $invArr["itms"][] = [
                            "num" => $key + 1,
                            "itm_det" => [
                                "txval" => floatval(round($taxableAmount, 2)),
                                "rt" => floatval(round($oneItem["tax"], 2)),
                                "camt" => floatval(round($cgst, 2)),
                                "samt" => floatval(round($sgst, 2)),
                                "csamt" => floatval(round($cessAmount, 2))
                            ]
                        ];
                    }
                }
                $custArr["inv"][] = $invArr;
            }
            $b2bArr[] = $custArr;
        }
        return $b2bArr;
    }

    private function getB2cl()
    {
        $invObj = queryGet('SELECT SUM(items.basePrice-items.totalDiscountAmt) AS totalTaxableAmt, items.tax AS taxRate, invoices.placeOfSupply FROM `erp_branch_sales_order_invoice_items` AS items LEFT JOIN `erp_branch_sales_order_invoices` AS invoices ON items.so_invoice_id=invoices.so_invoice_id WHERE invoices.all_total_amt>=200000 AND invoices.`customer_gstin`="" AND invoices.`invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '" GROUP BY invoices.placeOfSupply, items.tax', true);
        $b2cl = [];
        foreach ($invObj["data"] as $row) {
            $totalTax = $row["totalTaxableAmt"] * $row["taxRate"] / 100;
            $cgst = 0;
            $row["placeOfSupply"] = $row["placeOfSupply"] != "" ? $row["placeOfSupply"] : $this->branch_gstin_code;
            if ($this->branch_gstin_code == $row["placeOfSupply"]) {
                $b2cl[] = [
                    "sply_ty" => "INTRA",
                    "rt" => floatval(round($row["taxRate"], 2)),
                    "typ" => "OE",
                    "pos" => $row["placeOfSupply"],
                    "txval" => floatval(round($row["totalTaxableAmt"], 2)),
                    "camt" => floatval(round($totalTax / 2, 2)),
                    "samt" => floatval(round($totalTax / 2, 2)),
                    "csamt" => floatval(round($cgst, 2))
                ];
            } else {
                $b2cl[] = [
                    "sply_ty" => "INTER",
                    "rt" => floatval(round($row["taxRate"], 2)),
                    "typ" => "OE",
                    "pos" => $row["placeOfSupply"],
                    "txval" => floatval(round($row["totalTaxableAmt"], 2)),
                    "iamt" => floatval(round($totalTax, 2)),
                    "csamt" => floatval(round($cgst, 2))
                ];
            }
        }
        return $b2cl;
    }

    private function getB2cs()
    {
        $invObj = queryGet('SELECT SUM(items.basePrice-items.totalDiscountAmt) AS totalTaxableAmt, items.tax AS taxRate, invoices.placeOfSupply FROM `erp_branch_sales_order_invoice_items` AS items LEFT JOIN `erp_branch_sales_order_invoices` AS invoices ON items.so_invoice_id=invoices.so_invoice_id WHERE invoices.all_total_amt<200000 AND invoices.`customer_gstin`="" AND invoices.`invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '" GROUP BY invoices.placeOfSupply, items.tax', true);
        $b2cl = [];
        foreach ($invObj["data"] as $row) {
            $totalTax = $row["totalTaxableAmt"] * $row["taxRate"] / 100;
            $cgst = 0;
            $row["placeOfSupply"] = $row["placeOfSupply"] != "" ? $row["placeOfSupply"] : $this->branch_gstin_code;
            if ($this->branch_gstin_code == $row["placeOfSupply"]) {
                $b2cl[] = [
                    "sply_ty" => "INTRA",
                    "rt" => floatval(round($row["taxRate"], 2)),
                    "typ" => "OE",
                    "pos" => $row["placeOfSupply"],
                    "txval" => floatval(round($row["totalTaxableAmt"], 2)),
                    "camt" => floatval(round($totalTax / 2, 2)),
                    "samt" => floatval(round($totalTax / 2, 2)),
                    "csamt" => floatval(round($cgst, 2))
                ];
            } else {
                $b2cl[] = [
                    "sply_ty" => "INTER",
                    "rt" => floatval(round($row["taxRate"], 2)),
                    "typ" => "OE",
                    "pos" => $row["placeOfSupply"],
                    "txval" => floatval(round($row["totalTaxableAmt"], 2)),
                    "iamt" => floatval(round($totalTax, 2)),
                    "csamt" => floatval(round($cgst, 2))
                ];
            }
        }
        return $b2cl;
    }

    private function getDocs()
    {
        $listObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '"', true);

        $data = [];
        $data["doc_det"] = [];

        $data["doc_det"][] = [
            "doc_num" => 1,
            "doc_typ" => "Invoices for outward supply",
            "docs" => [
                [
                    "num" => 1,
                    "from" => $listObj["data"][0]["invoice_no"],
                    "to" => end($listObj["data"])["invoice_no"],
                    "totnum" => count($listObj["data"]),
                    "cancel" => 0,
                    "net_issue" => count($listObj["data"])
                ]
            ]
        ];
        return $data;
    }

    private function getHsn()
    {
        $complianceGSTR1ViewDataObj = new ComplianceGSTR1ViewData($this->periodStart, $this->periodEnd);
        $getHsnDataObj = $complianceGSTR1ViewDataObj->getHsnData();

        $hsnData = [];
        $hsnData["data"] = [];
        $sl = 0;
        foreach ($getHsnDataObj["data"] as $row) {
            $sl += 1;
            $hsnData["data"][] = [
                "num" => $sl,
                "hsn_sc" => $row['hsn'],
                "uqc" => "NA",
                "qty" => 0,
                "rt" => floatval($row["taxRate"]),
                "txval" => floatval($row["taxableValue"]),
                "iamt" => floatval($row["igst"] > 0 ? $row["igst"] : 0),
                "camt" => floatval($row["cgst"] > 0 ? $row["cgst"] : 0),
                "samt" => floatval($row["sgst"] > 0 ? $row["sgst"] : 0),
                "csamt" => 0
            ];
        }
        return $hsnData;
    }

    public function getCreditDebitNotes()
    {

        // data from creditnote table
        $creditNoteObj = queryGet("SELECT * FROM `erp_credit_note` WHERE `branch_id`=$this->branch_id AND `status`='active' AND `postingDate` BETWEEN '$this->periodStart' AND '$this->periodEnd'", true);

        $tempRegData = [];
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
                "ntty" => ($crDrNote["creditors_type"] == "vendor") ? "C" : "D",
                "nt_num" => $crDrNote["credit_note_no"],
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
                if ($partyGstin == "" || $this->branch_gstin_code == $crDrNote["destination_address"]) {
                    $tempItem["itm_det"]["camt"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]) / 2;
                    $tempItem["itm_det"]["samt"] = $tempItem["itm_det"]["camt"];
                } else {
                    $tempItem["itm_det"]["iamt"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]);
                }
                $tempItem["itm_det"]["csamt"] = 0;
                $tempNote["itms"][] = $tempItem;
            }

            if ($partyGstin != "") {
                $tempRegData[$partyGstin][] = $tempNote;
            } else {
                $tempUnregData[$partyCode][] = $tempNote;
            }
        }




        // data from debit table
        $debitNoteObj = queryGet("SELECT * FROM `erp_debit_note` WHERE `branch_id`=$this->branch_id AND `status`='active' AND `postingDate` BETWEEN '$this->periodStart' AND '$this->periodEnd'", true);

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
                if ($partyGstin == "" || $this->branch_gstin_code == $crDrNote["destination_address"]) {
                    $tempItem["itm_det"]["camt"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]) / 2;
                    $tempItem["itm_det"]["samt"] = $tempItem["itm_det"]["camt"];
                } else {
                    $tempItem["itm_det"]["iamt"] = ($oneItem["item_amount"] - $tempItem["itm_det"]["txval"]);
                }
                $tempItem["itm_det"]["csamt"] = 0;
                $tempNote["itms"][] = $tempItem;
            }

            if ($partyGstin != "") {
                $tempRegData[$partyGstin][] = $tempNote;
            } else {
                $tempUnregData[$partyCode][] = $tempNote;
            }
        }



        // generate summary



        $cdnr = [];
        foreach ($tempRegData as $regCtin => $tempData) {
            $cdnr[] = [
                "ctin" => $regCtin,
                "nt" => $tempData
            ];
        }

        $cdnur = [];
        foreach ($tempRegData as $regCtin => $tempData) {
            foreach ($tempData as $oneData) {
                unset($oneData["rchrg"]);
                unset($oneData["inv_typ"]);
                $oneData["typ"] = "B2CL";
                $cdnur[] = $oneData;
            }
        }








        // $cdnr = [
        //     [
        //         "ctin" => "01AAAAP1208Q1ZS",
        //         "nt" => [
        //             [
        //                 "ntty" => "C",
        //                 "nt_num" => "533515",
        //                 "nt_dt" => "23-09-2016",
        //                 "p_gst" => "N",
        //                 "pos" => "01",
        //                 "rchrg" => "N",
        //                 "inv_typ" => "R",
        //                 "val" => 123123,
        //                 "diff_percent" => 0.65,
        //                 "itms" => [
        //                     [
        //                         "num" => 1,
        //                         "itm_det" => [
        //                             "rt" => 10,
        //                             "txval" => 5225.28,
        //                             "iamt" => 339.64,
        //                             "csamt" => 789.52
        //                         ]
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];

        // $cdnur = [
        //     [
        //         "typ" => "B2CL",
        //         "ntty" => "C",
        //         "nt_num" => "533515",
        //         "nt_dt" => "23-09-2016",
        //         "p_gst" => "N",
        //         "pos" => "05",
        //         "val" => 64646,
        //         "diff_percent" => 0.65,
        //         "itms" => [
        //             [
        //                 "num" => 1,
        //                 "itm_det" => [
        //                     "rt" => 10,
        //                     "txval" => 5225.28,
        //                     "iamt" => 339.64,
        //                     "csamt" => 789.52
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];

        return [
            "cdnr" => $cdnr,
            "cdnur" => $cdnur,
        ];
    }


    function getJson()
    {
        // console([$this->company_id, $this->branch_id, $this->location_id, $this->created_by, $this->updated_by, $this->branch_gstin]);
        $jsonData = [
            "gstin" => $this->branch_gstin,
            "fp" => $this->periodGstr1,
            "gt" => 0.00,
            "cur_gt" => 0.00,
            "b2b" => $this->getB2b(),
            "b2cs" => $this->getb2cs(),
            "b2cl" => $this->getb2cl(),
            "doc_issue" => $this->getDocs(),
            "hsn" => $this->getHsn(),
            "cdnr" => $this->getCreditDebitNotes()["cdnr"],
            "cdnur" => $this->getCreditDebitNotes()["cdnur"],
        ];
        return $jsonData;
    }
}
