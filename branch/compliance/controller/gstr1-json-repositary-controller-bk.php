<?php
// require_once("../../../app/v1/connection-branch-admin.php");

class Gstr1JsonRepository
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


    private $b2bJsonDATA;
    private $b2csJsonDATA;
    private $b2clJsonDATA;
    private $hsnJsonDATA;
    private $docIssueJsonDATA;
    private $cdnrJsonDATA;
    private $cdnurJsonDATA;
    private $expJsonDATA;


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


    //SELECT `parent_slug`, `documentNo`, `documentDate`, `postingDate`, `journal_status` FROM `erp_acc_journal` WHERE `postingDate` BETWEEN '2024-02-01' AND '2024-02-29' AND `branch_id`=5 AND `parent_slug` IN ("SOInvoicing", "VendorCN", "VendorDN", "CustomerCN", "CustomerDN") ORDER BY `documentNo`, `documentDate`;

    function generate()
    {
        $invListObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '"', true);
        // console($invListObj);
        foreach ($invListObj["data"] as $oneInvoiceKey => $oneInvoice) {
            $invoiceItemsObj = queryGet('SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE `so_invoice_id`=' . $oneInvoice["so_invoice_id"] . ' AND `status`="active"', true);
            $invoiceItemListData = $invoiceItemsObj["data"] ?? [];

            // check cancel and reverse invoice
            if ($oneInvoice["status"] != "active") {
                continue;
            }

            //check hsn
            $this->genHsnJson($oneInvoice, $invoiceItemListData);


            if ($oneInvoice["compInvoiceType"] != "R") {

                //export
                $this->genExport($oneInvoice, $invoiceItemListData);

                continue;
            }

            // console($oneInvoice);
            // console($invoiceItemListData);

            //check b2b incoice
            if ($oneInvoice["customer_gstin"] != "") {
                $this->genB2bJson($oneInvoice, $invoiceItemListData);
            }

            //check b2cs invoice
            if ($oneInvoice["customer_gstin"] == "" && $this->helperAmount($oneInvoice["all_total_amt"]) < 200000 && $oneInvoice["compInvoiceType"] == "R") {
                // console($oneInvoice);
                $this->genB2csJson($oneInvoice, $invoiceItemListData);
            }

            //check b2cl invoice
            if ($oneInvoice["customer_gstin"] == "" && $this->helperAmount($oneInvoice["all_total_amt"]) >= 200000 && $oneInvoice["compInvoiceType"] == "R") {
                $this->genB2clJson($oneInvoice, $invoiceItemListData);
            }
        }

        // check docs
        $this->genDocIssue();

        //credit notes
        $this->genCreditDebitNotes();


        $jsonObj = [
            "gstin" => $this->branch_gstin,
            "fp" => $this->periodGstr1,
        ];

        if (count($this->b2bJsonDATA) > 0) {
            $jsonObj["b2b"] = $this->b2bJsonDATA;
        }

        if (count($this->b2csJsonDATA) > 0) {
            $jsonObj["b2cs"] = $this->b2csJsonDATA;
        }

        if (count($this->b2clJsonDATA) > 0) {
            $jsonObj["b2cl"] = $this->b2clJsonDATA;
        }

        if (count($this->cdnrJsonDATA) > 0) {
            $jsonObj["cdnr"] = $this->cdnrJsonDATA;
        }

        if (count($this->cdnurJsonDATA) > 0) {
            $jsonObj["cdnur"] = $this->cdnurJsonDATA;
        }

        if (count($this->expJsonDATA) > 0) {
            $jsonObj["exp"] = $this->expJsonDATA;
        }

        if (count($this->docIssueJsonDATA) > 0) {
            $jsonObj["doc_issue"]["doc_det"] = $this->docIssueJsonDATA;
        }


        if (count($this->hsnJsonDATA) > 0) {
            $newdocs = [];
            foreach ($this->hsnJsonDATA as $doc) {
                $doc["txval"] = $this->helperAmount($doc["txval"]);
                $doc["iamt"] = $this->helperAmount($doc["iamt"]);
                $doc["camt"] = $this->helperAmount($doc["camt"]);
                $doc["samt"] = $this->helperAmount($doc["samt"]);
                $newdocs[] = $doc;
            }
            $jsonObj["hsn"]["data"] = $newdocs;
        }

        //     "b2b" => $this->b2bJsonDATA,
        //     "b2cs" => $this->b2csJsonDATA,
        //     "b2cl" => $this->b2clJsonDATA,
        //     "hsn" => $this->hsnJsonDATA,
        //     "doc_issue" => $this->docIssueJsonDATA,
        //     "cdnr" => $this->cdnrJsonDATA,
        //     "cdnur" => $this->cdnurJsonDATA,
        //     "exp" => $this->expJsonDATA,

        return $jsonObj;
    }


    private function helperPos($pos)
    {
        if (intval($pos) < 10) {
            return "0" . intval($pos);
        } else {
            return intval($pos) . "";
        }
    }

    private function helperAmount($amt)
    {
        return round(floatval($amt), 2);
    }

    private function helperItem($item, $invoiceObj)
    {
        $iamt = 0;
        $samt = 0;
        $camt = 0;
        $sply_ty = "INTRA";
        $txval = $item["basePrice"] - $item["totalDiscountAmt"];
        $rt = $this->helperAmount($item["tax"]);

        if ($this->helperAmount($invoiceObj["igst"]) > 0) {
            $iamt = (($txval * $rt) / 100);
            $sply_ty = "INTER";
        } elseif ($this->helperAmount($invoiceObj["sgst"]) > 0) {
            $samt = (($txval * $rt) / 100) / 2;
            $camt = (($txval * $rt) / 100) / 2;
            $sply_ty = "INTRA";
        } else {
            //check the state of both buyer and seller
            $sellerStateCode = intval(substr($this->branch_gstin, 0, 2));
            $buyerStateCode = intval(substr($invoiceObj["customer_gstin"], 0, 2));
            if ($sellerStateCode == $buyerStateCode) {
                $samt = (($txval * $rt) / 100) / 2;
                $camt = (($txval * $rt) / 100) / 2;
            } else {
                $iamt = (($txval * $rt) / 100);
            }
        }
        return [
            "txval" => $this->helperAmount($txval),
            "rt" => $this->helperAmount($item["tax"]),
            "iamt" => $this->helperAmount($iamt),
            "camt" => $this->helperAmount($camt),
            "samt" => $this->helperAmount($samt),
            "csamt" => 0,
            "pos" => $this->helperPos($invoiceObj["placeOfSupply"]),
            "sply_ty" => $sply_ty,
        ];
    }

    private function helperDate($date)
    {
        return date("d-m-Y", strtotime($date));
    }

    private function helperValidateB2bItem($invoice)
    {
        $invoiceValue = $invoice["val"];
        $itemTotalValue = 0;
        foreach ($invoice["itms"] as $row) {
            $itm = $row["itm_det"];
            $itemTotalValue += $itm["txval"] + $itm["iamt"] + $itm["camt"] + $itm["samt"] + $itm["csamt"];
        }

        if ($invoiceValue === $itemTotalValue) {
            return $invoice;
        } else {
            $newValue = 0;
            foreach ($invoice["itms"] as $key => $row) {
                $itm = $invoice["itms"][$key]["itm_det"];
                $txval = round($itm["txval"]);
                $iamt = round($itm["iamt"]);
                $camt = round($itm["camt"]);
                $samt = round($itm["samt"]);

                $invoice["itms"][$key]["itm_det"]["txval"] = $txval;
                $invoice["itms"][$key]["itm_det"]["iamt"] = $iamt;
                $invoice["itms"][$key]["itm_det"]["camt"] = $camt;
                $invoice["itms"][$key]["itm_det"]["samt"] = $samt;

                $newValue += $txval + $iamt + $camt + $samt;
            }

            return $invoice;
            // if($newValue === $invoiceValue){
            //     return $invoice;
            // }else{
            //     return null;
            // }
        }
    }

    private function genB2bJson($invoiceObj, $itemListObj)
    {
        $data = [
            "inum" => $invoiceObj["invoice_no"],
            "idt" => $this->helperDate($invoiceObj["invoice_date"]),
            "val" => $this->helperAmount($invoiceObj["all_total_amt"]),
            "pos" => $this->helperPos($invoiceObj["placeOfSupply"]),
            "rchrg" => $invoiceObj["reverseCharge"],
            "inv_typ" => $invoiceObj["compInvoiceType"],
            "itms" => []
        ];

        foreach ($itemListObj as $itemKey => $item) {
            $newItem = $this->helperItem($item, $invoiceObj);
            $data["itms"][] = [
                "num" => $itemKey + 1,
                "itm_det" => [
                    "rt" => $newItem["rt"],
                    "txval" => $newItem["txval"],
                    "iamt" => $newItem["iamt"],
                    "camt" => $newItem["camt"],
                    "samt" => $newItem["samt"],
                    "csamt" => $newItem["csamt"],
                ]
            ];
        }



        $validatedB2bItem = $this->helperValidateB2bItem($data);
        $data = $validatedB2bItem;

        // console($validatedB2bItem);

        $existing_ctin_index = -1;
        foreach ($this->b2bJsonDATA as $index => $item) {
            if (isset($item['ctin']) && $item['ctin'] === $invoiceObj["customer_gstin"]) {
                $existing_ctin_index = $index;
                break;
            }
        }

        if ($existing_ctin_index !== -1) {
            $this->b2bJsonDATA[$existing_ctin_index]["inv"][] = $data;
        } else {
            $tempData["ctin"] = $invoiceObj["customer_gstin"];
            $tempData["inv"][] = $data;
            $this->b2bJsonDATA[] = $tempData;
        }
    }

    private function genB2csJson($invoiceObj, $itemListObj)
    {
        foreach ($itemListObj as $itemKey => $item) {
            $newItem = $this->helperItem($item, $invoiceObj);
            $data["typ"] = "OE";
            $data["sply_ty"] = $newItem["sply_ty"];
            $data["rt"] = $newItem["rt"];
            $data["pos"] = $newItem["pos"];
            $data["txval"] = $newItem["txval"];

            if ($newItem["sply_ty"] === "INTRA") {
                $data["camt"] = $newItem["camt"];
                $data["samt"] = $newItem["samt"];
                $data["csamt"] = $newItem["csamt"];
            } else {
                $data["iamt"] = $newItem["iamt"];
                $data["csamt"] = $newItem["csamt"];
            }

            $isPrevItemNotExist = true;
            foreach ($this->b2csJsonDATA as $prevItemKey => $prevItem) {
                if ($prevItem["typ"] === $data["typ"] && $prevItem["pos"] === $data["pos"] && $prevItem["rt"] === $data["rt"]) {
                    $isPrevItemNotExist = false;

                    $this->b2csJsonDATA[$prevItemKey]["txval"] += $data["txval"];
                    if ($data["sply_ty"] === "INTRA") {
                        $this->b2csJsonDATA[$prevItemKey]["camt"] += $data["camt"];
                        $this->b2csJsonDATA[$prevItemKey]["samt"] += $data["samt"];
                        $this->b2csJsonDATA[$prevItemKey]["csamt"] += $data["csamt"];
                    } else {
                        $this->b2csJsonDATA[$prevItemKey]["iamt"] += $data["iamt"];
                        $this->b2csJsonDATA[$prevItemKey]["csamt"] += $data["csamt"];
                    }
                    break;
                }
            }

            if ($isPrevItemNotExist) {
                $this->b2csJsonDATA[] = $data;
            }
        }
    }

    private function genB2clJson($invoiceObj, $itemListObj)
    {
        foreach ($itemListObj as $itemKey => $item) {
            $newItem = $this->helperItem($item, $invoiceObj);
            $data["typ"] = "OE";
            $data["sply_ty"] = $newItem["sply_ty"];
            $data["rt"] = $newItem["rt"];
            $data["pos"] = $newItem["pos"];
            $data["txval"] = $newItem["txval"];

            if ($newItem["sply_ty"] === "INTRA") {
                $data["camt"] = $newItem["camt"];
                $data["samt"] = $newItem["samt"];
                $data["csamt"] = $newItem["csamt"];
            } else {
                $data["iamt"] = $newItem["iamt"];
                $data["csamt"] = $newItem["csamt"];
            }

            $isPrevItemNotExist = true;
            foreach ($this->b2clJsonDATA as $prevItemKey => $prevItem) {
                if ($prevItem["typ"] === $data["typ"] && $prevItem["pos"] === $data["pos"] && $prevItem["rt"] === $data["rt"]) {
                    $isPrevItemNotExist = false;

                    $this->b2clJsonDATA[$prevItemKey]["txval"] += $data["txval"];
                    if ($data["sply_ty"] === "INTRA") {
                        $this->b2clJsonDATA[$prevItemKey]["camt"] += $data["camt"];
                        $this->b2clJsonDATA[$prevItemKey]["samt"] += $data["samt"];
                        $this->b2clJsonDATA[$prevItemKey]["csamt"] += $data["csamt"];
                    } else {
                        $this->b2clJsonDATA[$prevItemKey]["iamt"] += $data["iamt"];
                        $this->b2clJsonDATA[$prevItemKey]["csamt"] += $data["csamt"];
                    }
                    break;
                }
            }

            if ($isPrevItemNotExist) {
                $this->b2clJsonDATA[] = $data;
            }
        }
    }

    private function genHsnJson($invoiceObj, $itemListObj)
    {
        foreach ($itemListObj as $itemKey => $item) {
            $newItem = $this->helperItem($item, $invoiceObj);
            $newItem["hsn_sc"] = $item["hsnCode"] . "";

            $isPrevItemNotExist = true;
            foreach ($this->hsnJsonDATA as $prevItemKey => $prevItem) {
                if ($prevItem["hsn_sc"] === $newItem["hsn_sc"] && $prevItem["rt"] === $newItem["rt"]) {
                    $isPrevItemNotExist = false;
                    $this->hsnJsonDATA[$prevItemKey]["txval"] += $newItem["txval"];
                    $this->hsnJsonDATA[$prevItemKey]["iamt"] += $newItem["iamt"];
                    $this->hsnJsonDATA[$prevItemKey]["camt"] += $newItem["camt"];
                    $this->hsnJsonDATA[$prevItemKey]["samt"] += $newItem["samt"];
                    $this->hsnJsonDATA[$prevItemKey]["csamt"] += $newItem["csamt"];

                    break;
                }
            }

            if ($isPrevItemNotExist) {
                $this->hsnJsonDATA[] = [
                    "num" => $this->hsnJsonDATA ? count($this->hsnJsonDATA) + 1 : 1,
                    "hsn_sc" => $newItem["hsn_sc"],
                    "txval" => $newItem["txval"],
                    "iamt" => $newItem["iamt"],
                    "camt" => $newItem["camt"],
                    "samt" => $newItem["samt"],
                    "csamt" => $newItem["csamt"],
                    "desc" => "",
                    "uqc" => "NA",
                    "qty" => 0,
                    "rt" => $newItem["rt"]
                ];
            }
        }
    }

    private function genDocIssue()
    {
        $docsObj = queryGet('SELECT `invoice_no`, `inv_variant_id`, `status` FROM `erp_branch_sales_order_invoices` WHERE `branch_id`=' . $this->branch_id . ' AND `invoice_date` BETWEEN "' . $this->periodStart . '" AND "' . $this->periodEnd . '" AND `status` NOT IN("reposted", "reverse")', true);

        $tempData = [];
        foreach ($docsObj["data"] as $oneInv) {
            $tempData[$oneInv["inv_variant_id"]][] = $oneInv;
        }

        $tt = [];
        $sl=0;
        foreach ($tempData as $invoicesList) {

            $tt[] = [
                "num" => $sl+=1,
                "from" => $invoicesList[0]["invoice_no"],
                "to" => end($invoicesList)["invoice_no"],
                "totnum" => count($invoicesList),
                "cancel" => 0,
                "net_issue" => count($invoicesList)
            ];
        }
        $this->docIssueJsonDATA[] = [
            "doc_num" => 1,
            "docs" => $tt
        ];
    }


    private function genExport($invoiceObj, $itemListObj)
    {
        $tempData = [
            "inum" => $invoiceObj["invoice_no"],
            "idt" => $this->helperDate($invoiceObj["invoice_date"]),
            "val" => $this->helperAmount($invoiceObj["all_total_amt"]),
            "itms" => []
        ];

        foreach ($itemListObj as $itemKey => $item) {
            $newItem = $this->helperItem($item, $invoiceObj);
            $tempData["itms"][] = [
                "txval" => $newItem["txval"],
                "rt" => 0,
                "iamt" => 0,
                "csamt" => 0
            ];
        }

        $expType = "WOPAY";
        $expPrevIndex = -1;
        foreach ($this->expJsonDATA as $expDataKey => $expData) {
            if ($expData["exp_typ"] === $expType) {
                $expPrevIndex = $expDataKey;
                break;
            }
        }

        if ($expPrevIndex === -1) {
            // add new exp inv
            $newData["exp_typ"] = $expType;
            $newData["inv"][] = $tempData;
            $this->expJsonDATA[] = $newData;
        } else {
            // append to prev exp inv
            $this->expJsonDATA[$expPrevIndex]["inv"][] = $tempData;
        }
    }


    private function genCreditDebitNotes()
    {
        // data from creditnote table
        $creditNoteObj = queryGet("SELECT * FROM `erp_credit_note` WHERE `branch_id`=$this->branch_id AND `status`='active' AND `postingDate` BETWEEN '$this->periodStart' AND '$this->periodEnd'", true);

        $totalCreditNoteDoc = count($creditNoteObj["data"]);
        if ($totalCreditNoteDoc > 0) {
            $this->docIssueJsonDATA[] = [
                "doc_num" => 5,
                "docs" => [
                    [
                        "num" => 1,
                        "from" => $creditNoteObj["data"][0]["credit_note_no"],
                        "to" => end($creditNoteObj["data"])["credit_note_no"],
                        "totnum" => $totalCreditNoteDoc,
                        "cancel" => 0,
                        "net_issue" => $totalCreditNoteDoc
                    ]
                ],
            ];
        }


        foreach ($creditNoteObj["data"] as $creditNote) {
            $creditNoteId = $creditNote["cr_note_id"];
            $creditNoteItemObj = queryGet("SELECT cItems.*, iItems.hsnCode FROM `credit_note_item` as cItems LEFT JOIN `erp_inventory_items` AS iItems ON cItems.item_id=iItems.itemId WHERE cItems.`credit_note_id`=$creditNoteId", true);


            // console($creditNoteItemObj);

            $partyId = $creditNote["party_id"];
            $partyCode = $creditNote["party_code"];
            $partyGstin = "";
            $partyPan = "";
            if ($creditNote["creditors_type"] == "vendor") {
                $vendorObj = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $vendorObj["data"]["vendor_gstin"] ?? "";
                $partyPan = $vendorObj["data"]["vendor_pan"] ?? "";
            } else {
                $customerObj = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id`=$partyId AND `company_branch_id`=$this->branch_id");
                $partyGstin = $customerObj["data"]["customer_gstin"] ?? "";
                $partyPan = $customerObj["data"]["customer_pan"] ?? "";
            }

            // console($partyGstin);
            // echo "<br><br>";

            $tempNote = [
                "val" => $this->helperAmount($creditNote["total"]),
                "ntty" => ($creditNote["creditors_type"] == "vendor") ? "D" : "C",
                "nt_num" => $creditNote["credit_note_no"],
                "nt_dt" => $this->helperDate($creditNote["postingDate"]),
                "pos" => $this->helperPos($creditNote["destination_address"]),
                "rchrg" => "N",
                "inv_typ" => "R",
                "itms" => []
            ];

            foreach ($creditNoteItemObj["data"] as $oneItemKey => $oneItem) {
                $itemTotalTaxAmount = $this->helperAmount($oneItem["cgst"] + $oneItem["sgst"] + $oneItem["igst"]);

                $txval = $this->helperAmount($oneItem["item_amount"] - $itemTotalTaxAmount);
                $tempNote["itms"][] = [
                    "num" => $oneItemKey + 1,
                    "itm_det" => [
                        "txval" => $txval,
                        "rt" => $this->helperAmount($oneItem["item_tax"]),
                        "iamt" => $this->helperAmount($oneItem["igst"]),
                        "camt" => $this->helperAmount($oneItem["cgst"]),
                        "samt" => $this->helperAmount($oneItem["sgst"]),
                        "csamt" => 0
                    ]
                ];


                foreach ($this->hsnJsonDATA as $docKey => $doc) {
                    if ($doc["hsn_sc"] === $oneItem["hsnCode"] && $doc["rt"] == $this->helperAmount($oneItem["item_tax"])) {
                        $this->hsnJsonDATA[$docKey]["txval"] = $this->hsnJsonDATA[$docKey]["txval"] - $txval;
                        $this->hsnJsonDATA[$docKey]["iamt"] = $this->hsnJsonDATA[$docKey]["iamt"] - $oneItem["igst"];
                        $this->hsnJsonDATA[$docKey]["camt"] = $this->hsnJsonDATA[$docKey]["camt"] - $oneItem["cgst"];
                        $this->hsnJsonDATA[$docKey]["samt"] = $this->hsnJsonDATA[$docKey]["samt"] - $oneItem["sgst"];
                    }
                }
            }

            if ($partyGstin != "") {
                $prevCreditNoteIndex = -1;
                foreach ($this->cdnrJsonDATA as $creditNoteKey => $creditNoteData) {
                    if ($creditNoteData["ctin"] === $partyGstin) {
                        $prevCreditNoteIndex = $creditNoteKey;
                        break;
                    }
                }

                if ($prevCreditNoteIndex == -1) {
                    $this->cdnrJsonDATA[] = [
                        "ctin" => $partyGstin,
                        "nt" => [$tempNote],
                    ];
                } else {
                    $this->cdnrJsonDATA[$prevCreditNoteIndex]["nt"][] = $tempNote;
                }

                // console($this->cdnrJsonDATA);
                // console("==========================");
            } else {
                // echo "hello from unreg";
                $this->cdnurJsonDATA[] = $tempNote;
            }
        }
    }
}


// $gstr1JsonRepoObj = new Gstr1JsonRepository("022024", "2024-02-01", "2024-02-29");
// $jsonObj = $gstr1JsonRepoObj->generate();
// // console($jsonObj);

// header("content-type: application/json");
// echo json_encode($jsonObj, true);