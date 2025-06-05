<?php
require_once "../../connection-branch-admin.php";
require_once "func-journal.php";


class SubscriptionInvoiceController extends Accounting
{

    private function getInventoryItemParentGl($itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $pGlIdObj = queryGet('SELECT `parentGlId` FROM `erp_inventory_items` WHERE `company_id` =' . $company_id . ' AND `itemId` =' . $itemId);
        if ($pGlIdObj["numRows"] == 1) {
            return $pGlIdObj["data"]["parentGlId"];
        } else {
            return 0;
        }
    }

    function insertBranchInvoiceFromSo($POST)

    { 
       // echo 'okayyyyyyy';
       // console($POST);
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
        // $invNo = $POST['invoiceDetails']['invNo'] ?? 0;
        $invNo = $IvNoByVerientresponse['iv_number'];
        $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
        $pgi_id = $POST['invoiceDetails']['pgiId'] ?? 0;
        $pgi_no = $POST['invoiceDetails']['pgiNo'] ?? 0;
        $creditPeriod = $POST['invoiceDetails']['creditPeriod'];
        $delivery_no = $POST['invoiceDetails']['delivery_no'];
        $so_number = $POST['invoiceDetails']['so_number'];
        $customer_id = $POST['invoiceDetails']['customer_id'] ?? 0;
        $invoice_date = $POST['invoiceDetails']['invoiceDate'];
        $poNumber = $POST['invoiceDetails']['poNumber'];
        $poDate = $POST['invoiceDetails']['poDate'];
        $kamId = $POST['invoiceDetails']['kamId'] ?? 0;
        $shipToLastInsertedId = $POST['shipToLastInsertedId'];
        $profit_center = $POST['invoiceDetails']['profit_center'];
        $subTotal = $POST['invoiceDetails']['subTotal'] ?? 0;
        $totalTaxAmt = $POST['invoiceDetails']['totalTaxAmt'] ?? 0;
        $cgst = $POST['invoiceDetails']['cgst'] ?? 0;
        $sgst = $POST['invoiceDetails']['sgst'] ?? 0;
        $igst = $POST['invoiceDetails']['igst'] ?? 0;
        $tcs = $POST['invoiceDetails']['tcs'] ?? 0;
        $totalDiscount = $POST['invoiceDetails']['totalDiscount'] ?? 0;
        $allTotalAmt = $POST['invoiceDetails']['allTotalAmt'] ?? 0;
        $totalItems = $POST['invoiceDetails']['totalItems'] ?? 0;
        $customer_billing_address = $POST['invoiceDetails']['customer_billing_address'];
        $customer_shipping_address = $POST['invoiceDetails']['customer_shipping_address'];
        $bankId = $POST['bankId'] ?? 0;

        $curr_rate = 1;
        if ($POST['curr_rate']) {
            $curr_rate = $POST['curr_rate'];
        }

        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0] ?? 0;
        $currencyName = $currency[2];

        $branchGstin = $POST['branchGstin'];

        $company_logo = $POST['companyDetails']['company_logo'];
        $address = $POST['companyDetails']['address'];
        $signature = $POST['companyDetails']['signature'];
        $footerNote = $POST['companyDetails']['footerNote'];

        $customerName = $POST['customerDetails']['name'];
        $customerGstin = $POST['customerDetails']['gstin'];
        $customerPhone = $POST['customerDetails']['phone'];
        $customerEmail = $POST['customerDetails']['email'];
        $customerAddress = $POST['customerDetails']['address'];

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customer_id'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["data"]["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["data"]["parentGlId"] ?? 0;

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $company_name = $companyDetailsObj['company_name'];

        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `customer_id`='$customer_id',
                            `credit_period`='$creditPeriod',
                            `delivery_no`='$delivery_no',
                            `so_number`='$so_number',
                            `invoice_date`='$invoice_date',
                            `po_number`='$poNumber',
                            `po_date`='$poDate',
                            `shipToLastInsertedId`='$shipToLastInsertedId',
                            `totalItems`='$totalItems',
                            `sub_total_amt`='$subTotal',
                            `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                            `totalDiscount`='$totalDiscount',
                            `cgst`='$cgst',
                            `sgst`='$sgst',
                            `kamId`='$kamId',
                            `profit_center`='$profit_center',
                            `igst`='$igst',
                            `total_tax_amt`='$totalTaxAmt',
                            `all_total_amt`='$allTotalAmt',
                            `due_amount`='$allTotalAmt',
                            `customerDetails`='$customerDetailsSerialize',
                            `companyDetails`='$companySerialize',
                            `company_bank_details`='$companyBankSerialize',
                            `company_gstin`='$branchGstin',
                            `customer_gstin`='$customerGstin',
                            `customer_billing_address`='$customer_billing_address',
                            `customer_shipping_address`='$customer_shipping_address',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by',
                            `type`='so',
                            `invoiceStatus`='1'
        ";
        // console($invInsert);
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];

            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invId'";
            $getInvNumber =  queryGet($sql)['data']['invoice_no'];

            $encodeInv_id = base64_encode($invId);

            $StUpd = 'UPDATE `' . ERP_BRANCH_SALES_ORDER . '` SET `approvalStatus`= 10 WHERE so_number="' . $so_number . '"';
            $updateStatus = queryUpdate($StUpd);


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_INVOICES;
            $auditTrail['basicDetail']['column_name'] = 'so_invoice_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $invId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customer_id;
            $auditTrail['basicDetail']['document_number'] = $invNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Invoice Creation ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Invoice Details']['invoice_no'] = $invNo;
            $auditTrail['action_data']['Invoice Details']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Invoice Details']['invoice_date'] = $invoice_date;
            $auditTrail['action_data']['Invoice Details']['totalItems'] = $totalItems;
            $auditTrail['action_data']['Invoice Details']['sub_total_amt'] = $subTotal;
            $auditTrail['action_data']['Invoice Details']['totalDiscount'] = $totalDiscount;
            $auditTrail['action_data']['Invoice Details']['cgst'] = $cgst;
            $auditTrail['action_data']['Invoice Details']['sgst'] = $sgst;
            $auditTrail['action_data']['Invoice Details']['igst'] = $igst;
            $auditTrail['action_data']['Invoice Details']['kamId'] = $kamId;
            $auditTrail['action_data']['Invoice Details']['total_tax_amt'] = $totalTaxAmt;
            $auditTrail['action_data']['Invoice Details']['all_total_amt'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['due_amount'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['customerDetails'] = $customerDetailsSerialize;
            $auditTrail['action_data']['Invoice Details']['companyDetails'] = $companySerialize;
            $auditTrail['action_data']['Invoice Details']['company_bank_details'] = $companyBankSerialize;
            $auditTrail['action_data']['Invoice Details']['company_gstin'] = $branchGstin;
            $auditTrail['action_data']['Invoice Details']['customer_gstin'] = $customerGstin;
            $auditTrail['action_data']['Invoice Details']['customer_billing_address'] = $customer_billing_address;
            $auditTrail['action_data']['Invoice Details']['customer_shipping_address'] = $customer_shipping_address;



            // update delivery pgi table
            // $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
            //             SET
            //                 invoiceStatus=1 WHERE so_delivery_pgi_id='" . $pgi_id . "' ";
            // $dbCon->query($upd);
            $flug = 0;

            foreach ($listItem as $itemKey => $item) {
                $lineNo = $item['lineNo'];
                $inventory_item_id = $item['inventory_item_id'] ?? 0;
                $itemCode = $item['itemCode'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $hsnCode = $item['hsnCode'];
                $tax = $item['tax'] ?? 0;
                $totalTax = $item['totalTax'] ?? 0;
                $tolerance = $item['tolerance'] ?? 0;
                if (!empty(trim($tolerance))) {
                    $tolerance = $tolerance;
                } else {
                    $tolerance = 0;
                }
                $totalDiscount = $item['totalDiscount'] ?? 0;
                $totalDiscountAmt = $item['totalDiscountAmt'] ?? 0;
                $goodsMainPrice = $item['goodsMainPrice'] ?? 0;
                $unitPrice = $item['unitPrice'] ?? 0;
                $qty = $item['qty'] ?? 0;
                $basePrice = ($qty * $unitPrice);
                $uom = $item['uom'];
                $totalPrice = $item['totalPrice'] ?? 0;
                $delivery_date = $item['delivery_date'] ?? 0;
                $enterQty = $item['enterQty'] ?? 0;
                $listItem[$itemKey]["parentGlId"] = $this->getInventoryItemParentGl($item["inventory_item_id"]);
                $invItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                            SET
                            `so_invoice_id`='$invId',
                            `inventory_item_id`='" . $inventory_item_id . "',
                            `lineNo`='" . $lineNo . "',
                            `itemCode`='" . $itemCode . "',
                            `itemName`='" . $itemName . "',
                            `itemDesc`='" . $itemDesc . "',
                            `delivery_date`='" . $delivery_date . "',
                            `qty`='" . $qty . "',
                            `uom`='" . $uom . "',
                            `tolerance`='" . $tolerance . "',
                            `goodsMainPrice`='" . $goodsMainPrice . "',
                            `unitPrice`='" . $unitPrice . "',
                            `basePrice`='" . $basePrice . "',
                            `hsnCode`='" . $hsnCode . "',
                            `tax`='" . $tax . "',
                            `totalTax`='" . $totalTax . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `totalDiscountAmt`='" . $totalDiscountAmt . "',
                            `createdBy`='" . $created_by . "',
                            `updatedBy`='" . $updated_by . "',
                            `totalPrice`='" . $totalPrice . "'
                ";
                // console($invItem);
                if ($dbCon->query($invItem)) {
                    $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                    $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $basePrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                    $return['status'] = "success";
                    $return['message'] = "Invoice Created Successfully";
                    $return['invoiceNo'] = $getInvNumber;

                    $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgMktOpen`= `fgMktOpen`-" . $qty . " WHERE itemId='" . $item['inventory_item_id'] . "'";
                    $updateItemStocksObj = queryUpdate($upd);
                    ///---------------------------------Audit Log Start---------------------
                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                    $auditTrailSummry = array();
                    $auditTrailSummry['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                    $auditTrailSummry['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                    $auditTrailSummry['basicDetail']['column_name'] = 'itemId'; // Primary key column
                    $auditTrailSummry['basicDetail']['document_id'] = $item['inventory_item_id'];  //     primary key
                    $auditTrailSummry['basicDetail']['document_number'] = $itemCode;
                    $auditTrailSummry['basicDetail']['action_code'] = $action_code;
                    $auditTrailSummry['basicDetail']['action_referance'] = $invNo;
                    $auditTrailSummry['basicDetail']['action_title'] = 'Item Stock added';  //Action comment
                    $auditTrailSummry['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                    $auditTrailSummry['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                    $auditTrailSummry['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                    $auditTrailSummry['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                    $auditTrailSummry['basicDetail']['action_sqlQuery'] = base64_encode($upd);
                    $auditTrailSummry['basicDetail']['others'] = '';
                    $auditTrailSummry['basicDetail']['remark'] = '';

                    $auditTrailSummry['action_data']['Summary']['fgMktOpen'] = $qty * -1;

                    $auditTrailreturn = generateAuditTrail($auditTrailSummry);
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong! 3";
                    $flug++;
                }
            }
            if ($flug == 0) {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
                $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;
                $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $customer_id");
                $mailValid = $customer_sql['data']['isMailValid'] ;
                if($mailValid == 'yes'){
                
                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Invoice details:
                </strong>
                <div style="display:grid">
                    <span>
                        Invoice Number: ' . $invNo . '
                    </span>
                    <span>
                        Amount Due: ' . $allTotalAmt . '
                    </span>
                    <span>
                        Due Date: <strong>' . $duedate . '</strong>
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                <p>
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);

                }
                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);

                if ($mail == true) {
                    $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `mailStatus`='1' WHERE `so_invoice_id`='$invId'
                        ";
                    if ($dbCon->query($sql)) {
                        $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                                        SET
                                            `company_id`='$company_id',
                                            `branch_id`='$branch_id',
                                            `location_id`='$location_id',
                                            `so_invoice_id`='$invId',
                                            `mailStatus`='1',
                                            `created_by`='$created_by',
                                            `updated_by`='$updated_by'";
                        $dbCon->query($ins);
                    }
                }

                $auditTrail['action_data']['Mail Details']['Status'] = 'Mail send Successfully';

                $auditTrailreturn = generateAuditTrail($auditTrail);


                $itemQtyMin = '-' . $qty;
                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $company_id . "',
                            branchId = '" . $branch_id . "',
                            locationId = '" . $location_id . "',
                            storageLocationId = 9,
                            storageType = 'FG-MKT',
                            itemId = '" . $item['inventory_item_id'] . "',
                            itemQty = '" . $itemQtyMin . "',
                            itemUom = '" . $uom . "',
                            itemPrice = '" . $unitPrice . "',
                            logRef = '" . $pgi_no . "',
                            createdBy = '" . $created_by . "',
                            updatedBy = '" . $updated_by . "'
                ";
                $dbCon->query($insStockSummary2);

                $flug2 = 0;
                //************************START ACCOUNTING ******************** */

                //-----------------------------PGI ACC Start ----------------
                $PGIInputData = [
                    "BasicDetails" => [
                        "documentNo" => $pgi_no, // Invoice Doc Number
                        "documentDate" => $invoice_date, // Invoice number
                        "postingDate" => $invoice_date, // current date
                        "reference" => $invNo, // grn code
                        "remarks" => "PGI Creation - " . $invNo,
                        "journalEntryReference" => "Sales"
                    ],
                    "FGItems" => $listItem
                ];
                //console($ivPostingInputData);
                $ivPostingObj = $this->sopgiAccountingPosting($PGIInputData, "PGI", $pgi_id);
                //console($ivPostingObj);
                if ($ivPostingObj['status'] == 'success') {
                    $pgiJournalId = $ivPostingObj['journalId'];
                    $sqlpgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "`
                                    SET
                                        `journal_id`=$pgiJournalId 
                                    WHERE `so_delivery_pgi_id`='$pgi_id'  ";

                    queryUpdate($sqlpgi);
                    //-----------------------------PGI ACC END ----------------

                    //-----------------------------Invoicing ACC Start ----------------
                    $InvoicingInputData = [
                        "BasicDetails" => [
                            "documentNo" => $pgi_no, // Invoice Doc Number
                            "documentDate" => $invoice_date, // Invoice number
                            "postingDate" => $invoice_date, // current date
                            "grnJournalId" => $pgiJournalId,
                            "reference" => $invNo, // grn code
                            "remarks" => "SO Invoicing - " . $invNo,
                            "journalEntryReference" => "Sales"
                        ],
                        "customerDetails" => [
                            "customerId" => $customer_id,
                            "customerName" => $customerName,
                            "customerCode" => $customerCode,
                            "customerGlId" => $customerParentGlId
                        ],
                        "FGItems" => $listItem,
                        "taxDetails" => [
                            "cgst" => $cgst,
                            "sgst" => $sgst,
                            "igst" => $igst,
                            "TCS" => $tcs
                        ]
                    ];
                    //console($ivPostingInputData);
                    $SOivPostingObj = $this->soIvAccountingPosting($InvoicingInputData, "SOInvoicing", $invId);
                    // console($SOivPostingObj);

                    if ($ivPostingObj['status'] == 'success') {
                        $ivJournalId = $SOivPostingObj['journalId'];
                        $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `pgi_journal_id`=$pgiJournalId,
                                        `journal_id`=$ivJournalId 
                                        WHERE `so_invoice_id`='$invId'";
                        queryUpdate($sqliv);
                    } else {
                        $flug2++;
                    }

                    //-----------------------------Invoicing ACC END ----------------

                } else {
                    $flug2++;
                }
                if ($flug2 == 0) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Invoice Created Successfully";
                    $returnData['invoiceNo'] = $getInvNumber;
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice successfully sent, (Warning Account Entry failed!)";
                    $returnData['journal_sql'] = $SOivPostingObj;
                    $returnData['journal_sql2'] = $ivPostingObj;
                }
                //************************END ACCOUNTING ******************** */
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong! 01";
            $returnData['sql'] = $invInsert;
        }
        return $returnData;
    }
    
// add branch SO delivery 
function itemQtyStockCheck($item_id, $stockLoc, $ordering = 'ASC', $refNumber = null, $asondate = null,$company_id,$branch_id,$location_id)
{

 

   
    if (empty($asondate)) {
        $asondate = date("Y-m-d");
    }
    $cond = '';
    // if (!empty($refNumber)) {
    //     $cond .= " AND log.refNumber IN ($refNumber)";
    // }

    if (!empty($refNumber)) {
        $cond .= " AND CONCAT(log.logRef, log.storageLocationId) IN ($refNumber)";
    }

    //$selStockLog = "SELECT loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,SUM(log.itemQty) as itemQty,log.itemUom,log.logRef,grn.postingDate FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_grn AS grn ON log.logRef=grn.grnCode WHERE log.companyId=$company_id AND log.branchId=$branch_id AND log.locationId=$location_id AND log.itemId=$item_id AND grn.postingDate BETWEEN '2023-06-01' AND '" . $today . "' AND loc.storageLocationTypeSlug IN('rmWhOpen','rmWhReserve','fgWhOpen') GROUP BY loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,log.itemUom,log.logRef,grn.postingDate ORDER BY grn.postingDate ASC";
    $selStockLog = "SELECT
             warh.warehouse_id,
             warh.warehouse_code,
             warh.warehouse_name,
             loc.storage_location_id,
             loc.storage_location_code,
             loc.storage_location_name,
             loc.storage_location_type,
             loc.storageLocationTypeSlug,
             (SELECT SUM(itemQty) FROM erp_inventory_stocks_log WHERE storageLocationId=log.storageLocationId AND logRef= log.logRef AND itemId=log.itemId AND companyId=log.companyId AND branchId=log.branchId AND locationId=log.locationId) AS itemQty,
             log.logRef,
             log.bornDate,
             MAX(log.itemPrice) as itemPrice,
             CONCAT(log.logRef, log.storageLocationId) AS logRefConcat -- Concatenate logRef and storageLocationId
         FROM
             erp_inventory_stocks_log AS log
         LEFT JOIN erp_storage_location AS loc
         ON
             log.storageLocationId = loc.storage_location_id
         LEFT JOIN erp_storage_warehouse AS warh
         ON
             loc.warehouse_id=warh.warehouse_id             
         WHERE
         log.companyId=$company_id 
         AND log.branchId=$branch_id 
         AND log.locationId=$location_id 
         AND log.itemId=$item_id 
         AND loc.storageLocationTypeSlug IN($stockLoc)
         AND log.storageType IN($stockLoc)
         AND log.bornDate <= '$asondate' 
         $cond 
         GROUP BY
             loc.storage_location_id,
             loc.storage_location_code,
             loc.storage_location_name,
             loc.storage_location_type,
             loc.storageLocationTypeSlug,
             log.logRef,
             log.bornDate
         HAVING itemQty > 0
         ORDER BY
             log.bornDate $ordering";


    $getStockLog = queryGet($selStockLog, true);
    // return $getStockLog;

    $totquantities = array_column($getStockLog['data'], "itemQty");
    $itemOpenStocks = array_sum($totquantities);
    if ($itemOpenStocks == '') {
        $itemOpenStocks = '0';
    }
    $getStockLog['sumOfBatches'] = $itemOpenStocks;

    return $getStockLog;
}

// add invoice 
function insertBranchDirectInvoice($POST, $FILES = null)
{
    global $dbCon;
    $returnData = [];

    // console($POST);
    $so = $POST['so'];
    $company_id = $so['company_id'];
    $branch_id = $so['branch_id'];
    $location_id = $so['location_id'];
    $created_by = $so['created_by'];
    $updated_by = $so['updated_by'];
    // console($so);
    // exit();
    $customerId = $so['customer_id'] ?? 0;
    $billingAddress = cleanUpString(addslashes($so['billingAddress']));
    $shippingAddress = cleanUpString(addslashes($so['shippingAddress']));
    $creditPeriod = $so['credit_period'];
    $invoice_date = $so['so_date'];
    $invoiceTime = $so['soPostingTime'];
    $declaration_note = addslashes($so['remarks']);
    $billing_address_id = $so['billing_address_id'] ?? 0;
    $shipping_address_id = $so['shipping_address_id'] ?? 0;
    $profitCenter = $so['profit_center'];
    $kamId = $so['kamId'] ?? 0;
    $so_id = $so['so_id'];
    $remarks = addslashes($so['remarks']);
    $customerType = null;
    if (isset($so['walkInCustomerCheckbox'])) {
        $customerType = "walkin";
        $customerId = 0;
    } // need to check

    $companyConfigId = 0;
    if (isset($so['companyConfigId']) && $so['companyConfigId'] != 0) {
        $companyConfigId = $so['companyConfigId'];
    } // need to check

    $iv_varient = $POST['iv_varient'] ?? 0; // need to check
    $shipToLastInsertedId = $so['shipToLastInsertedId'] ?? 0;
    $bankId = $POST['bankId'] ?? 0; // need to check
    $compInvoiceType = $POST['compInvoiceType'] ?? 0; // need to check
    $placeOfSupply = $POST['placeOfSupply'] ?? 0; // need to check
    $customerGstinCode = $POST['customerGstinCode'] ?? 0; // need to check
    $quotationId = base64_decode($POST['quotationId']) ?? 0; // need to check
    $pgi_to_invoice = base64_decode($POST['pgi_to_invoice']) ?? 0; // need to check
    $so_to_invoice = base64_decode($POST['so_to_invoice']) ?? 0; // need to check
    $ivType = $POST['ivType'] ?? ' '; // need to check

    $curr_rate = $so['conversion_rate'];
    //$currency = explode('≊', $so['currency']);
    $currencyId = $so['currency_id'];
    $currencyName = $so['currency_name'];

    

    $totalDiscount = str_replace(',', '', $so['totalDiscount']) ?? 0;
    $totalCashDiscount = str_replace(',', '', $so['totalCashDiscount']) ?? 0;

    $totalTaxAmt = str_replace(',', '', $so['totalTax']) ?? 0;

   

    // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
    $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");
    // uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)

    $allTotalAmt = $so['totalAmount'];

    $subTotal = $allTotalAmt-($totalDiscount + $totalCashDiscount + $totalTaxAmt);
    
    $roundOffValue = 0;
    // if ($POST['round_off_checkbox'] == 1 || !empty($POST['roundOffValue'])) {
    //     // $allTotalAmt = $POST['adjustedTotalAmount'] ?? 0;
    //     $allTotalAmt = $POST['paymentDetails']['adjustedCollectAmount'] ?? 0;
    //     $roundOffValue = $POST['paymentDetails']['roundOffValue'] ?? 0;
    // } else {
    //     $roundOffValue = 0;
    //     $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']) ?? 0;
    // }

    $totalItems = $so['totalItems'];
    // $company_logo = $POST['companyDetails']['company_logo'];
    // $gstin = $POST['companyDetails']['gstin'];
    // $address = $POST['companyDetails']['address'];
    // $signature = $POST['companyDetails']['signature'];
    // $footerNote = $POST['companyDetails']['footerNote'];

    $customerName = addslashes($POST['customerDetails']['name']) ?? 0;  // need to check
    // $customerGstin = $POST['customerDetails']['gstin'];
    // $customerPhone = $POST['customerDetails']['phone'];
    // $customerEmail = $POST['customerDetails']['email'];
    // $customerAddress = $POST['customerDetails']['address'];

    $invNo = '';
    if (isset($POST['repostInvoiceNo']) && !empty($POST['repostInvoiceNo'])) {
        $invNo = $POST['repostInvoiceNo'];
    }

    // fetch customer details
    
//   echo  $customerDetailsObj = "SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'";

    $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
   // console($customerDetailsObj);
    $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
    $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];
  // $customer_authorised_person_phone = 8910533689;
    
    $customer_name = addslashes($customerDetailsObj['customer_name']);

    $customerDetailsSerialize = serialize($customerDetailsObj);
    $customerCode = $customerDetailsObj["customer_code"] ?? 0;
    $customerParentGlId = $customerDetailsObj["parentGlId"] ?? 0;
    $customerName = addslashes($customerDetailsObj['customer_name']);
    $customer_Gst = $customerDetailsObj['customer_gstin'];

    // fetch company details
    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
    $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
    $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
    $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
    $companySerialize = serialize($arrMarge);
    $companySerialize = str_replace(["\r", "\n"], '', $companySerialize);

    $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
    $companyBankSerialize = serialize($companyBankAccDetailsObj);

    $branch_Gst = $branchDetailsObj['branch_gstin'];

    $companyGstCode = substr($branch_Gst, 0, 2);
    $customerGstCode = substr($customer_Gst, 0, 2);

    $cgst = 0;
    $sgst = 0;
    $igst = 0;
    $tcs = 0;

    $gstAmt = 0;
    if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
        if ($customerGstinCode != "") {
            if ($companyGstCode == $customerGstCode) {
                $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                $cgst = str_replace(',', '', $gstAmt);
                $sgst = str_replace(',', '', $gstAmt);
            } else {
                $igst = str_replace(',', '', $totalTaxAmt);
            }
        } else {
            if ($companyGstCode == $placeOfSupply) {
                $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                $cgst = str_replace(',', '', $gstAmt);
                $sgst = str_replace(',', '', $gstAmt);
            } else {
                $igst = str_replace(',', '', $totalTaxAmt);
            }
        }
    }
    // console('$companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply');
    // console($companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply);
    //  `invoice_no_serialized`='$invoice_no_serialized',
    $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='$ivType',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `kamId`='$kamId',
                             `invoice_date`='$invoice_date',
                             `invoice_time`='$invoiceTime',
                             `pgi_id`='$pgi_to_invoice',
                             `so_id`='$so_to_invoice',
                             `quotationId`='$quotationId',
                             `sub_total_amt`='$subTotal',
                             `profit_center`='$profitCenter',
                             `totalDiscount`='$totalDiscount',
                             `totalCashDiscount`='$totalCashDiscount',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `adjusted_amount`='$roundOffValue',
                             `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                             `total_tax_amt`='$totalTaxAmt',
                             `cgst`='$cgst',
                             `sgst`='$sgst',
                             `igst`='$igst',
                             `billing_address_id`='$billing_address_id',
                             `shipping_address_id`='$shipping_address_id',
                             `shipToLastInsertedId`='$shipToLastInsertedId',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `companyConfigId`='$companyConfigId',
                             `company_bank_details`='$companyBankSerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `placeOfSupply`='$placeOfSupply',
                             `compInvoiceType`='$compInvoiceType',
                             `declaration_note`='$declaration_note',
                             `remarks`='$remarks',
                             `customerType`='$customerType',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";

    // console($invInsert);
    if ($dbCon->query($invInsert)) {
        $returnData['lastID'] = $dbCon->insert_id;
        $inv_id = $dbCon->insert_id;
        $encodeInv_id = base64_encode($inv_id);

        if ($POST['walkInCustomerCheckbox'] == true) {
            if ($POST['walkInCustomerName'] != "" && $POST['walkInCustomerMobile'] != "") {
                $walkInCustomerName = $POST['walkInCustomerName'] ?? '';
                $walkInCustomerMobile = $POST['walkInCustomerMobile'] ?? '';

                $insertWalkInCustomer = "INSERT INTO `" . ERP_WALK_IN_INVOICES . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `invoice_id`='$inv_id',
                            `customer_name`='$walkInCustomerName',
                            `customer_phone`='$walkInCustomerMobile'
                    ";

                $insertWalkInCustomerObj = queryInsert($insertWalkInCustomer);
            }
        }

        if (!isset($POST['repostInvoiceNo'])) {



            // added to manual inv no 

            $invoiceNumberType = $so['invoiceNumberType'] ?? 0; // need to check
            $invoice_no_serialized = "";


            if ($invoiceNumberType == "manual") {
                $invNo = $_POST['ivnumberManual'];
                $invoice_no_serialized = "";
            } else {

                $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
                $invNo = $IvNoByVerientresponse['iv_number'];
                $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
            }

            $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized'
                         WHERE so_invoice_id='$inv_id'";
            queryUpdate($updateInv);
        } else {
            $invNo = $POST['repostInvoiceNo'];
            $repostInvoiceId = $POST['repostInvoiceId'];
            $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET
                            `invoice_no`='$invNo'
                         WHERE so_invoice_id='$inv_id'";
            queryUpdate($updateInv);

            $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET  
                            `status`='reposted'
                         WHERE so_invoice_id='$repostInvoiceId'";
            queryUpdate($updateInv);
        }

        ///---------------------------------Audit Log Start---------------------
        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail = array();
        $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_INVOICES;
        $auditTrail['basicDetail']['column_name'] = 'so_invoice_id'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $inv_id;  // primary key
        $auditTrail['basicDetail']['party_type'] = 'customer';
        $auditTrail['basicDetail']['party_id'] = $customerId;
        $auditTrail['basicDetail']['document_number'] = $invNo;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = 'Invoice Creation ';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';


        $auditTrail['action_data']['Invoice Details']['invoice_no'] = $invNo;
        $auditTrail['action_data']['Invoice Details']['credit_period'] = $creditPeriod;
        $auditTrail['action_data']['Invoice Details']['invoice_date'] = $invoice_date;
        $auditTrail['action_data']['Invoice Details']['totalItems'] = $totalItems;
        $auditTrail['action_data']['Invoice Details']['sub_total_amt'] = $subTotal;
        $auditTrail['action_data']['Invoice Details']['totalDiscount'] = $totalDiscount;
        $auditTrail['action_data']['Invoice Details']['cgst'] = $cgst;
        $auditTrail['action_data']['Invoice Details']['sgst'] = $sgst;
        $auditTrail['action_data']['Invoice Details']['igst'] = $igst;
        $auditTrail['action_data']['Invoice Details']['kamId'] = $kamId;
        $auditTrail['action_data']['Invoice Details']['total_tax_amt'] = $totalTaxAmt;
        $auditTrail['action_data']['Invoice Details']['all_total_amt'] = $allTotalAmt;
        $auditTrail['action_data']['Invoice Details']['due_amount'] = $allTotalAmt;
        $auditTrail['action_data']['Invoice Details']['customerDetails'] = $customerDetailsSerialize;
        $auditTrail['action_data']['Invoice Details']['companyDetails'] = $companySerialize;
        $auditTrail['action_data']['Invoice Details']['company_bank_details'] = $companyBankSerialize;
        $auditTrail['action_data']['Invoice Details']['company_gstin'] = $branch_Gst;
        $auditTrail['action_data']['Invoice Details']['customer_gstin'] = $customer_Gst;
        $auditTrail['action_data']['Invoice Details']['customer_billing_address'] = $billingAddress;
        $auditTrail['action_data']['Invoice Details']['customer_shipping_address'] = $shippingAddress;

        // insert attachment
        if ($attachmentObj['status'] == 'success') {
            $name = $attachmentObj['data'];
            $type = $FILES['attachment']['type'];
            $size = $FILES['attachment']['size'];
            $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

            $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='invoice-creation',
                            `ref_no`='$inv_id'
                ";
            $insertAttachment = queryInsert($insertAttachmentSql);
        }

        $listItem = $POST['listItem'];

        $invId = $returnData['lastID'];
        $flug = 0;

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invId'";
        $getInvNumber =  queryGet($sql)['data']['invoice_no'];
        $invTotalItems = 0;
        $pgiitem = [];
        foreach ($listItem as $items) {
            $item = $items[0];
          //  console($item);

            if ($item['goodsType'] != 5) {
                $pgiitem[] = $item;
            }

            $invTotalItems++;
            $lineNo = $item['lineNo'];
          $itemId = $item['inventory_item_id'];
            $invStatus = $item['invStatus'];
            $itemCode = $item['itemCode'];
            $goodsType = $item['goodsType'];
            $itemName = addslashes($item['itemName']);
            $itemDesc = addslashes($item['itemDesc']);
            $itemRemarks = addslashes($item['itemRemarks']) ?? '';

            $itemTradeDiscountPercentage = $item['totalDiscount'] ?? 0;
            $itemTradeDiscountAmount = $item['itemTotalDiscount'] ?? 0;
            $cashDiscountAmount = $item['cashDiscountAmount'] ?? 0;
            $cashDiscountPercentage = $item['cashDiscountPercentage'] ?? 0;

            $hsnCode = $item['hsnCode'];
            $tax = $item['tax'];
            $totalTax = $item['totalTax'];
            if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                
                $tax = $item['tax'];
                //$totalTax = str_replace(',', '', $item['itemTotalTax1']) ?? 0;
                $totalTax = str_replace(',', '', $item['totalTax']) ?? 0;
            
            }
            $tolerance = $item['tolerance'] ?? 0;
            $totalDiscount = $item['totalDiscount'] ?? 0;
            $totalDiscountAmt = str_replace(',', '', $item['itemTotalDiscount']) ?? 0;
            $unitPrice = str_replace(',', '', $item['itemTargetPrice']) ?? 0;
            // $baseAmount = str_replace(',', '', $item['baseAmount']) ? str_replace(',', '', $item['baseAmount']) : 0;
            $qty = $item['qty'] ?? 0;
            $baseAmount = $unitPrice*$qty;
            //$invoiceQty = $item['invoiceQty'] ?? 0;
            $invoiceQty = $item['qty'] ?? 0;
            
            $remainingQty = $item['remainingQty'] ?? 0;

            $uom = $item['uom'];
            $totalPrice = str_replace(',', '', $item['totalPrice']) ?? 0;
            $delivery_date = $item['delivery_date'];
            $enterQty = $item['enterQty'];

            $itemTargetPrice = $item['unitPrice'];  // itemTargetPrice(mrp) for invoice  

            $stockQty = $item['stockQty'] ?? 0 ; // need to check
            $explodeStockQty = $stockQty;
            if (isset($item['itemreleasetype'])) {
                if ($item["itemreleasetype"] == 'FIFO') {
                    $itemSellType = 'ASC';
                } else if ($item["itemreleasetype"] == 'LIFO') {
                    $itemSellType = 'DESC';
                } else if ($item["itemreleasetype"] == 'CUSTOM') {
                    $itemSellType = 'CUSTOM';
                    $batchselection = $item['batchselection'];
                }
            } else {
                if ($item["itemSellType"] == 'FIFO') {
                    $itemSellType = 'ASC';
                } else if ($item["itemSellType"] == 'LIFO') {
                    $itemSellType = 'DESC';
                } else if ($item["itemSellType"] == 'CUSTOM') {
                    //$itemSellType = 'ASC';
                }
            }

            $pgiNo = $_POST['pgiCode'];
               
                if ($ivType == "pgi_to_invoice") {
              
                $selStockLog = $this->itemQtyStockCheck($itemId, "'fgMktOpen'", "ASC", "", $invoice_date,$company_id,$branch_id,$location_id);
                $itemOpenStocks = $selStockLog['sumOfBatches'];
            } else {
                
                if ($itemSellType != 'CUSTOM') {
                    $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", $itemSellType, '', $invoice_date,$company_id,$branch_id,$location_id);
                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                } else {
                    // echo $itemCode;

                    $filteredBatchSelection = [];

                    foreach ($batchselection as $key => $value) {
                        $explodes = explode('_', $key);
                        $logRef = $explodes[0];
                        $slocation = $explodes[1];

                        $keysval = $logRef . $slocation;

                        if (!empty($value)) {
                            $filteredBatchSelection[$keysval] = $value;
                        }
                    }

                    $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


                    $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", 'ASC', "$keysString", $invoice_date,$company_id,$branch_id,$location_id);
                    // console($selStockLog);
                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                }
            }
            if ($goodsType == "5") {
               
                $invItem1 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                    SET
                    `so_invoice_id`='$invId',
                    `inventory_item_id`='$itemId',
                    `lineNo`='" . $lineNo . "',
                    `itemCode`='" . $itemCode . "',
                    `itemName`='" . $itemName . "',
                    `itemRemarks`='" . $itemRemarks . "',
                    `itemDesc`='" . $itemDesc . "',
                    `delivery_date`='" . $delivery_date . "',
                    `qty`='" . $qty . "',
                    `invoiceQty`='" . $invoiceQty . "',
                    `remainingQty`='" . $remainingQty . "',
                    `uom`='" . $uom . "',
                    `unitPrice`='" . $unitPrice . "',
                    `hsnCode`='" . $hsnCode . "',
                    `basePrice`='" . $baseAmount . "',
                    `tax`='" . $tax . "',
                    `totalTax`='" . $totalTax . "',
                    `totalDiscount`='" . $itemTradeDiscountPercentage . "',
                    `totalDiscountAmt`='" . $itemTradeDiscountAmount . "',
                    `cashDiscount`='" . $cashDiscountPercentage . "',
                    `cashDiscountAmount`='" . $cashDiscountAmount . "',
                    `createdBy`='" . $created_by . "',
                    `updatedBy`='" . $updated_by . "',
                    `itemTargetPrice`='" . $itemTargetPrice . "',
                    `totalPrice`='" . $totalPrice . "'";

                $itemIns = queryInsert($invItem1);
                if ($itemIns['status'] == 'success') {
                    $return['status'] = "success";
                    $return['message'] = "Item Insert Success!";

                    // $updateSalesItems = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET remainingQty=$remainingQty WHERE so_id=$so_id AND inventory_item_id=$itemId";
                    // $updateSalesItemsObj = queryUpdate($updateSalesItems);

                    $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                    $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $baseAmount;
                    $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message1'] = "somthing went wrong! 31";
                    $returnData['invItem'] = $itemOpenStocks;
                    $flug++;
                }
            } else {
            //   echo 1111100000000000000;
                $returnData['insStockreturn3'][] = $selStockLog;
                if ($itemOpenStocks >= $qty) {
                    $invItem1 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                             SET
                             `so_invoice_id`='$invId',
                             `inventory_item_id`='$itemId',
                             `lineNo`='" . $lineNo . "',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `itemDesc`='" . $itemDesc . "',
                             `itemRemarks`='" . $itemRemarks . "',
                             `delivery_date`='" . $delivery_date . "',
                             `qty`='" . $qty . "',
                             `invoiceQty`='" . $invoiceQty . "',
                             `remainingQty`='" . $remainingQty . "',
                             `uom`='" . $uom . "',
                             `unitPrice`='" . $unitPrice . "',
                             `hsnCode`='" . $hsnCode . "',
                             `basePrice`='" . $baseAmount . "',
                             `tax`='" . $tax . "',
                             `totalTax`='" . $totalTax . "',
                             `totalDiscount`='" . $itemTradeDiscountPercentage . "',
                             `totalDiscountAmt`='" . $itemTradeDiscountAmount . "',
                             `cashDiscount`='" . $cashDiscountPercentage . "',
                             `cashDiscountAmount`='" . $cashDiscountAmount . "',
                             `createdBy`='" . $created_by . "',
                             `updatedBy`='" . $updated_by . "',
                             `itemTargetPrice`='" . $itemTargetPrice . "',
                             `totalPrice`='" . $totalPrice . "'";

                            $itemIns = queryInsert($invItem1);
                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                        // $updateSalesItems = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET remainingQty=$remainingQty WHERE so_id=$so_id AND inventory_item_id=$itemId";
                        // $updateSalesItemsObj = queryUpdate($updateSalesItems);

                        $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                        $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                        $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                        $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                        $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                        $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                        $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                        $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $baseAmount;
                        $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                        $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                              

                        if ($ivType == "pgi_to_invoice") {
                           
                            foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                if ($qty <= 0) {
                                    break;
                                }
                                $quantity = $logdata['itemQty'];
                                $usedQuantity = min($quantity, $qty);
                                $qty -= $usedQuantity;

                                $logRef = $logdata['logRef'];
                                $bornDate = $logdata['bornDate'];

                                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    storageLocationId = '" . $logdata['storage_location_id'] . "',
                                                    storageType ='" . $logdata['storageLocationTypeSlug'] . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $unitPrice . "',
                                                    refActivityName='INVOICE',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $invNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $invoice_date . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                $insStockreturn1 = queryInsert($insStockSummary1);


                                $return['insStockreturn1'][] = $insStockreturn1;
                            }
                        } else {
                            
                            foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                if ($itemSellType == 'CUSTOM') {
                                    // $explodes = explode('_', $logdata['logRef']);
                                    // $logRef = $explodes[0];
                                    $logRef = $logdata['logRef'];
                                    $keysval = $logdata['logRefConcat'];
                                    $usedQuantity = $filteredBatchSelection[$keysval];
                                    $bornDate = $logdata['bornDate'];
                                    $storage_location_id = $logdata['storage_location_id'];
                                    $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                } else {
                                    if ($qty <= 0) {
                                        break;
                                    }

                                    $quantity = $logdata['itemQty'];
                                    $usedQuantity = min($quantity, $qty);
                                    $qty -= $usedQuantity;
                                    // $explodes = explode('_', $logdata['logRef']);
                                    // $logRef = $explodes[0];

                                    $logRef = $logdata['logRef'];
                                    $bornDate = $logdata['bornDate'];
                                    $storage_location_id = $logdata['storage_location_id'];
                                    $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                }

                                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $unitPrice . "',
                                                    refActivityName='INVOICE',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $invNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $invoice_date . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                $insStockreturn1 = queryInsert($insStockSummary1);
                                // console($insStockreturn1);

                                $returnData['insStockreturn1'][] = $insStockreturn1;
                                $returnData['insStockreturn2'][] = $selStockLog;
                            }
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message1'] = "somthing went wrong! 31";
                        $returnData['invItem'] = $itemOpenStocks;
                        $flug++;
                    }
                } else {
                    //echo 'else';
                    $returnData['status'] = "warning";
                    $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                    $flug++;
                }
            }
            if ($ivType == "project") {
                $invStatusUpdate = queryUpdate("UPDATE `erp_branch_sales_order_items` SET `invStatus`='done' WHERE `so_id`=$so_id AND `inventory_item_id`=$itemId");
            }
        }

        $updInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` SET totalItems='$invTotalItems' WHERE so_invoice_id='$invId'";
        $dbCon->query($updInv);

        // getNextSerializedCode 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾
        $declarationCode = rand(0000, 9999);

        $declarationObj = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='$ivType'");
        //console($declarationObj);

        if ($declarationObj['numRows'] > 0) {
            $updateDeclaration = "UPDATE `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET descText='$declaration_note' WHERE declarationType='$ivType'";
            $updateDeclarationObj = queryUpdate($updateDeclaration);
        } else {
            $insertDeclaration = "INSERT INTO `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `code`='$declarationCode',
                            `declarationType`='$ivType',
                            `descText`='$declaration_note',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'";
            $insertDeclarationObj = queryInsert($insertDeclaration);
        }
        // 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾

        // select from ERP_CUSTOMER_INVOICE_LOGS
        $selectInvLog = "SELECT * FROM `" . ERP_CUSTOMER_INVOICE_LOGS . "` WHERE customer_id=$customerId";
        $selectInvLogData = queryGet($selectInvLog);
        if ($selectInvLogData['numRows'] > 0) {
            //echo 1;
            // update customer log
            //`customerOrderNo`='$customerPO',
             $updateInvLog = "UPDATE `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                                SET
                                    `company_id`=$company_id,
                                    `branch_id`=$branch_id,
                                    `location_id`=$location_id,
                                    `customer_id`=$customerId,
                                    `ref_no`='$getInvNumber',
                                    `profit_center`='$profitCenter',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `bank`='$bankId',
                                    `invoiceNoFormate`='$iv_varient',
                                    `placeOfSupply`='$placeOfSupply',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$curr_rate',
                                    `currency_id`='$currencyId',
                                    `currency_name`='$currencyName',
                                    `billingAddress`='$billingAddress',
                                    `shippingAddress`='$shippingAddress',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by' WHERE customer_id=$customerId";
            $updateInvoiceLog = queryInsert($updateInvLog);
        } else {
            //echo 2;
            // insert customer logs
            //`customerOrderNo`='$customerPO',
            $insInvLog = "INSERT INTO `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                            SET
                                `company_id`=$company_id,
                                `branch_id`=$branch_id,
                                `location_id`=$location_id,
                                `customer_id`=$customerId,
                                `ref_no`='$getInvNumber',
                                `profit_center`='$profitCenter',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `bank`='$bankId',
                                `invoiceNoFormate`='$iv_varient',
                                `placeOfSupply`='$placeOfSupply',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$curr_rate',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
            $invoiceLog = queryInsert($insInvLog);
        }

        if ($ivType == "pgi_to_invoice") {
            // update pgi
            $updatePgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                            SET 
                                `pgiStatus`='invoice' WHERE so_delivery_pgi_id=" . $pgi_to_invoice . "";
            queryUpdate($updatePgi);
        } elseif ($ivType == "quotation_to_invoice") {
            // update quotations
            $updateQuoat = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` 
                SET 
                `approvalStatus`=10 WHERE quotation_id=" . $quotationId . "";
            queryUpdate($updateQuoat);
        } elseif ($ivType == "so_to_invoice") {
            // update so
            $updateSo = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                SET 
                `soStatus`=10 WHERE so_id=" . $so_to_invoice . "";
            queryUpdate($updateSo);
        }
      
        if ($flug == 0) {
          
            // calculate days to date
            $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
            $invoicelink = BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;
            $invoicelinkWhatsapp = 'classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;
            $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $customerId");
            $mailValid = $customer_sql['data']['isMailValid'] ;
            if($mailValid == 'yes'){
           $to = $customer_authorised_person_email;
         // $to = "ssengupta@vitwo.in";
            $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
            $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
                <div><strong>Dear ' . $customerName . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $customerName . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Invoice details:
                </strong>
                <div style="display:grid">
                    <span>
                        Invoice Number: ' . $invNo . '
                    </span>
                    <span>
                        Amount Due: ' . $allTotalAmt . '
                    </span>
                    <span>
                        Due Date: <strong>' . $duedate . '</strong>
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $customerName . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $customerName . '</b></span>
                </div>
                
                <p>
                <a href="' . $invoicelink . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
            $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);
            }

            global $current_userName;
            $whatsapparray = [];
            $whatsapparray['templatename'] = 'invoice_sent_msg';
           $whatsapparray['to'] = $customer_authorised_person_phone;
            $whatsapparray['customername'] = $customer_name;
            $whatsapparray['invoiceno'] = $invNo;
            $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
            $whatsapparray['quickcontact'] = null;
            $whatsapparray['current_userName'] = $current_userName;

            $whatsappreturn = SendMessageByWhatsappTemplate($whatsapparray);

            if ($mail == true) {
                $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                     SET
                                         `mailStatus`='1' WHERE `so_invoice_id`='$invId'
                         ";
                if ($dbCon->query($sql)) {
                    $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                                         SET
                                             `company_id`='$company_id',
                                             `branch_id`='$branch_id',
                                             `location_id`='$location_id',
                                             `so_invoice_id`='$invId',
                                             `mailStatus`='1',
                                             `created_by`='$location_id',
                                             `updated_by`='$location_id' 
                             ";
                    $dbCon->query($ins);
                }
            }
            $auditTrail['action_data']['Mail Details']['Status'] = 'Mail send Successfully';

            $auditTrailreturn = generateAuditTrail($auditTrail);

            $flug2 = 0;


            //************************START ACCOUNTING ******************** */
            $extra_remark = $POST['extra_remark'] ?? '';
            
            if (count($pgiitem) > 0) {
                //-----------------------------PGI ACC Start ----------------
                $PGIInputData = [
                    "BasicDetails" => [
                        "documentNo" => $invNo, // Invoice Doc Number
                        "documentDate" => $invoice_date, // Invoice number
                        "postingDate" => $invoice_date, // current date
                        "reference" => $invNo, // grn code
                        "remarks" => "PGI Creation - " . $invNo . " " . $extra_remark,
                        "journalEntryReference" => "Sales"
                    ],
                    "customerDetails" => [
                        "customerId" => $customerId,
                        "customerName" => $customerName,
                        "customerCode" => $customerCode,
                        "customerGlId" => $customerParentGlId
                    ],
                    "FGItems" => $pgiitem
                ];
                //console($ivPostingInputData);
                $ivPostingObj = $this->sopgiAccountingPosting($PGIInputData, "PGI", 0);
                //console($ivPostingObj); 

                if ($ivPostingObj['status'] == 'success') {
                    $pgiJournalId = $ivPostingObj['journalId'];
                    $sqlpgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "`
                                    SET
                                        `journal_id`=$pgiJournalId 
                                    WHERE `so_delivery_pgi_id`='$invNo'  ";

                    queryUpdate($sqlpgi);

                    $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                SET
                                `pgi_journal_id`=$pgiJournalId
                                    WHERE `so_invoice_id`='$invId'";
                    queryUpdate($sqliv);
                } else {
                    $flug2++;
                }

                //-----------------------------PGI ACC END ----------------
            }


            //-----------------------------Invoicing ACC Start ----------------
            $InvoicingInputData = [
                "BasicDetails" => [
                    "documentNo" => $invNo, // Invoice Doc Number
                    "documentDate" => $invoice_date, // Invoice number
                    "postingDate" => $invoice_date, // current date
                    "grnJournalId" => '',
                    "reference" => $invNo, // grn code
                    "remarks" => "SO Invoicing - " . $invNo . " " . $extra_remark,
                    "journalEntryReference" => "Sales",
                    "invItem1 =>>" => $invItem1
                ],
                "customerDetails" => [
                    "customerId" => $customerId,
                    "customerName" => $customerName,
                    "customerCode" => $customerCode,
                    "customerGlId" => $customerParentGlId
                ],
                "companyDetails" => $arrMarge,
                "compInvoiceType" => $compInvoiceType,
                "FGItems" => $listItem,
                "taxDetails" => [
                    "cgst" => $cgst,
                    "sgst" => $sgst,
                    "igst" => $igst,
                    "TCS" => $tcs
                ],
                "roundOffValue" => $roundOffValue
            ];
           // console($ivPostingInputData);
            $SOivPostingObj = $this->soIvAccountingPosting($InvoicingInputData, "SOInvoicing", $invId);
          //  console($SOivPostingObj);

            if ($SOivPostingObj['status'] == 'success') {
                $ivJournalId = $SOivPostingObj['journalId'];
                $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `journal_id`=$ivJournalId 
                                        WHERE `so_invoice_id`='$invId'";
                queryUpdate($sqliv);
            } else {
                $flug2++;
            }

            //-----------------------------Invoicing ACC END ----------------
            //    $returnData['updateSalesItemsObj'] = $updateSalesItemsObj;
           
           // echo 'end';
            if ($flug2 == 0) {
                $returnData['type'] = "pos_invoice";
                $returnData['status'] = "success";
                $returnData['message'] = "Invoice Created Successfully";
                $returnData['invoiceLog'] = $invoiceLog;
                $returnData['insInvLog'] = $insInvLog;
                $returnData['updateInvoiceLog'] = $updateInvoiceLog;
                $returnData['updateInvLog'] = $updateInvLog;
                $returnData['invoiceNo'] = $getInvNumber;
                $returnData['updateQuoat'] = $updateQuoat;
                $returnData['PGIInputData'] = $PGIInputData;
                $returnData['InvoicingInputData'] = $InvoicingInputData;
                $returnData['ivPostingObj'] = $ivPostingObj;
                $returnData['SOivPostingObj'] = $SOivPostingObj;
               // console($returnData);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Invoice successfully sent, (Warning Account Entry failed!)";
            }
            //************************END ACCOUNTING ******************** */
        } else {
            //echo "not 0";
            $returnData['status'] = "warning";
            $returnData['message_03'] = "somthing went wrong! 30";
            $returnData['message'] = "Out of stock";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['invInsert'] = $invInsert;
        $returnData['message'] = "somthing went wrong! 2";
    }
    echo json_encode($returnData);
}
function subscriptionInvoice()
    {

       echo "ok";
       exit();
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $todayDate = date("Y-m-d");
        $subscriptionInvoiceData = queryGet("SELECT * FROM `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` WHERE next_trigger_date='$todayDate'", true);
       // console($subscriptionInvoiceData);

        if ($subscriptionInvoiceData['status'] == "success") {
            foreach ($subscriptionInvoiceData['data'] as $one) {
                if ($todayDate <= $one['end_on'] || $one['end_on'] == "") {
                    $soDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='" . $one['so_id'] . "'");
                   // console($soDetailsObj);
                    $soDetails = $soDetailsObj['data'];

                    $soItemDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $soDetails['so_id'] . "'", true);
                   // console($soItemDetailsObj);
                    $POST['listItem'] = $soItemDetailsObj['data'];

                    $POST['invoiceDetails']['invNo'] = "INV" . rand(0000, 9999);    //Ramen questions
                    $POST['invoiceDetails']['customer_id'] = $soDetails['customer_id'];
                    $POST['invoiceDetails']['creditPeriod'] = $soDetails['credit_period'];
                    $POST['invoiceDetails']['invoiceDate'] = $one['next_trigger_date'];
                    $POST['invoiceDetails']['so_number'] = $soDetails['so_number'];
                    $POST['invoiceDetails']['profit_center'] = $soDetails['profit_center'];
                    $POST['invoiceDetails']['kamId'] = $soDetails['kamId'];

                    $POST['curr_rate'] = $soDetails['conversion_rate'];
                    $POST['currency'] = $soDetails['currency_name'];

                    $POST['invoiceDetails']['totalItems'] = $soDetails['totalItems'] ?? 0;
                    $POST['invoiceDetails']['totalDiscount'] = $soDetails['totalDiscount'] ?? 0;
                    $POST['invoiceDetails']['subTotal'] = $soDetails['subTotal'] ?? 0;
                    $POST['invoiceDetails']['totalTaxAmt'] = $soDetails['totalTaxAmt'] ?? 0;
                    $POST['invoiceDetails']['cgst'] = $soDetails['cgst'] ?? 0;
                    $POST['invoiceDetails']['sgst'] = $soDetails['sgst'] ?? 0;
                    $POST['invoiceDetails']['igst'] = $soDetails['igst'] ?? 0;
                    $POST['invoiceDetails']['allTotalAmt'] = $soDetails['totalAmount'] ?? 0;
                    $POST['invoiceDetails']['customer_billing_address'] = $soDetails['billingAddress'];
                    $POST['invoiceDetails']['customer_shipping_address'] = $soDetails['shippingAddress'];

                  //  console($POST);

                    $invpostreturn = $this->insertBranchInvoiceFromSo($POST);
                    if ($invpostreturn['status'] == "success") {
                        //echo 'ok';
                        $days = $one['repeat_every'];
                        $nextMonth = date('Y-m-d', strtotime("+$days days"));

                        $subs = "UPDATE `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` SET `next_trigger_date`='$nextMonth' WHERE so_id='" . $soDetails['so_id'] . "'";
                        $data = queryUpdate($subs);
                        $returnData['message'] = "invoice sent successfully";
                        // return [
                        //     "status" => "success",
                        //     "message" => "success",
                        //     "sql" => $subs
                        // ];
                    } else {
                       // echo 'not';
                        $returnData['message'] = "somthing went wrong!";
                        // return [
                        //     "status" => "warning",
                        //     "message" => "warning",
                        //     "post" => $POST,
                        //     "resp" => $invpostreturn
                        // ];
                    }
                } else {
                    $returnData['message'] = "No subscribtion found in the record!";
                    // return [
                    //     "status" => "warning",
                    //     "message" => "No subscribtion found in the record!"
                    // ];
                }
            }
        } else {
            return [
                "status" => "warning",
                "message" => "No subscribtion date found in the record!",
                "sql" => $subscriptionInvoiceData,
            ];
        }
        return $returnData;
    }

}

// function invoiceCreationBySoId($soId = null)
// {
//     global $dbCon;
//     $returnData = [];

//     // Fetch sales order details
//     $so = queryGet("SELECT * FROM `erp_branch_sales_order` WHERE `so_id`='$soId'");
//     $returnData['so'] = $so['data'];

//     // Fetch line items for the sales order
//     $so_items = queryGet("SELECT * FROM `erp_branch_sales_order_items` WHERE `so_id`='$soId'", true);
//     $returnData['listItem'] = [];

//     foreach ($so_items['data'] as $so_item) {

//         $so_item_id = $so_item['so_item_id'];
//         $randCode = $so_item_id . rand(00, 99);
//         $returnData['listItem'][$randCode][] = $so_item;
//     }
//        $controller = new SubscriptionInvoiceController();
//        $controller->insertBranchDirectInvoice($returnData);
// }

// $inv = new SubscriptionInvoiceController();

// $returnData = $inv->subscriptionInvoice(); // Call the function with a specific $soId

// echo json_encode($returnData);