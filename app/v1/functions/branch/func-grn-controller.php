<?php
include("app/v1/functions/common/func-common.php");
require_once "func-journal.php";
require_once "func-branch-failed-accounting-controller.php";
class GrnController extends Accounting
{
    // ERP_GRN
    // ERP_GRN_GOODS
    private $failedAccController;

    public function __construct()
    {
        $this->failedAccController = new FailedAccController();
    }
    function getCompanyDetails()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $getCompanyDetails = queryGet('SELECT * FROM `erp_companies` WHERE `company_id`="' . $company_id . '"');
        return $getCompanyDetails["data"];
    }
    function getbalance($glid, $to_date)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $to_date = $to_date;
        $from_date_qry = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = " . $company_id . "");
        $from_date = $from_date_qry['data']['opening_date'];
        $subgl_id_qry = queryGet("SELECT * FROM `erp_acc_bank_cash_accounts` WHERE `parent_gl` = " . $glid . " AND `company_id` = " . $company_id . "");
        $gl_id = $subgl_id_qry['data']['parent_gl'];
        $code = $subgl_id_qry['data']['acc_code'];
        $opening = queryGet("
  WITH OpeningBalance AS (
      SELECT  
          gl, 
          subgl, 
          MIN(date) AS first_date, 
          SUM(opening_val) AS total_opening_value
      FROM 
          erp_opening_closing_balance AS eocb
      WHERE 
          company_id = $company_id 
          AND branch_id = $branch_id 
          AND location_id = $location_id 
          AND date = (
              SELECT MIN(date) 
              FROM erp_opening_closing_balance AS inner_eocb
              WHERE gl = eocb.gl 
                AND subgl = eocb.subgl
                AND company_id = $company_id 
                AND branch_id = $branch_id 
                AND location_id = $location_id
              LIMIT 1
          )
      GROUP BY 
          gl, subgl
  ),
  Debits AS (
      SELECT 
          ed.glId AS gl, 
          ed.subGlCode AS subGlCode, 
          ed.subGlName AS subGlName,
          SUM(ed.debit_amount) AS total_debit_value
      FROM 
          erp_acc_debit AS ed
      JOIN 
          erp_acc_journal AS ej ON ed.journal_id = ej.id
      WHERE 
          ej.postingDate >= (
              SELECT MIN(first_date) 
              FROM OpeningBalance 
              WHERE gl = ed.glId AND subGlCode = ed.subGlCode
          ) 
          AND ej.postingDate < '" . $from_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ed.glId, ed.subGlCode
  ),
  Credits AS (
      SELECT 
          ec.glId AS gl, 
          ec.subGlCode AS subGlCode, 
          ec.subGlName AS subGlName,
          SUM(ec.credit_amount) AS total_credit_value
      FROM 
          erp_acc_credit AS ec
      JOIN 
          erp_acc_journal AS ej ON ec.journal_id = ej.id
      WHERE 
          ej.postingDate >= (
              SELECT MIN(first_date) 
              FROM OpeningBalance 
              WHERE gl = ec.glId AND subGlCode = ec.subGlCode
          ) 
          AND ej.postingDate < '" . $from_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ec.glId, ec.subGlCode
  ),
  RangeDebits AS (
      SELECT 
          ed.glId AS gl, 
          ed.subGlCode AS subGlCode, 
          ed.subGlName AS subGlName,
          SUM(ed.debit_amount) AS final_debit_value
      FROM 
          erp_acc_debit AS ed
      JOIN 
          erp_acc_journal AS ej ON ed.journal_id = ej.id
      WHERE 
          ej.postingDate >= '" . $from_date . "' 
          AND ej.postingDate <= '" . $to_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ed.glId, ed.subGlCode
  ),
  RangeCredits AS (
      SELECT 
          ec.glId AS gl, 
          ec.subGlCode AS subGlCode, 
          ec.subGlName AS subGlName,
          SUM(ec.credit_amount) AS final_credit_value
      FROM 
          erp_acc_credit AS ec
      JOIN 
          erp_acc_journal AS ej ON ec.journal_id = ej.id
      WHERE 
          ej.postingDate >= '" . $from_date . "' 
          AND ej.postingDate <= '" . $to_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ec.glId, ec.subGlCode
  )
  SELECT  
      ob.gl, 
      coa.gl_code,
      coa.gl_label,
      ob.subgl,
     (ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) AS from_opening_value,
      COALESCE(rd.final_debit_value, 0) AS final_debit,
      COALESCE(rc.final_credit_value, 0) AS final_credit,
      ((ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) + COALESCE(rd.final_debit_value, 0) - COALESCE(rc.final_credit_value, 0)) AS to_closing_value
  FROM 
      OpeningBalance AS ob
  LEFT JOIN 
      Debits AS d ON ob.gl = d.gl AND ob.subgl = d.subGlCode
  LEFT JOIN 
      Credits AS c ON ob.gl = c.gl AND ob.subgl = c.subGlCode
  LEFT JOIN 
      RangeDebits AS rd ON ob.gl = rd.gl AND ob.subgl = rd.subGlCode
  LEFT JOIN 
      RangeCredits AS rc ON ob.gl = rc.gl AND ob.subgl = rc.subGlCode
  LEFT JOIN 
      `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON ob.gl = coa.id 
  WHERE ob.subgl = '$code'
  ORDER BY 
      ob.gl, ob.subgl");

        $opening_balance = $opening['data']['from_opening_value'];
        $sum_sql = queryGet("SELECT SUM(temp_table.debit) as debit_sum,SUM(temp_table.credit) as credit_sum FROM (SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,debit.subGlCode,debit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.glId= $gl_id AND debit.subGlCode = '" . $code . "' GROUP BY debit.subGlCode,debit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc
        UNION
        SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,credit.subGlCode,credit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.glId= $gl_id AND credit.subGlCode = '" . $code . "' GROUP BY credit.subGlCode,credit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc) AS temp_table");

        $debit_sum = round($sum_sql['data']['debit_sum'], 2) ?? 0;
        $credit_sum = round($sum_sql['data']['credit_sum'], 2) ?? 0;
        $balance_due = $opening_balance + ($debit_sum - $credit_sum);
        return $balance_due;
    }

    // function createGrn($INPUTS){
    //     global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;
    //     $returnData = [];
    //     $isValidate = validate($INPUTS, [
    //         "invoicePostingDate" => "required",
    //         "invoiceDueDate" => "required",
    //         "vendorCode" => "required",
    //         "vendorName" => "required",
    //         "vendorGstin" => "required",
    //         "totalInvoiceCGST" => "required",
    //         "totalInvoiceSGST" => "required",
    //         "totalInvoiceIGST" => "required",
    //         "totalInvoiceSubTotal" => "required",
    //         "totalInvoiceTotal" => "required",
    //         "grnItemCode" => "array",
    //         "grnItemHsn" => "array",
    //         "grnItemName" => "array",
    //         "grnItemQty" => "array",
    //         "grnItemTax" => "array",
    //         "grnItemUnitPrice" => "array",
    //         "grnItemTotalPrice" => "array",
    //         "grnItemReceivedQty" => "array"
    //     ]);
    //     if ($isValidate["status"] != "success") {
    //         $returnData['status'] = "warning";
    //         $returnData['message'] = "Invalid form inputes";
    //         $returnData['errors'] = $isValidate["errors"];
    //         return $returnData;
    //     }   
    //     $grnPoNumber = $INPUTS["invoicePoNumber"];
    //     $grnCode = $INPUTS["grnCode"];
    //     $documentNo = $INPUTS["documentNo"];
    //     $documentDate = $INPUTS["documentDate"];
    //     $invoicePostingDate = $INPUTS["invoicePostingDate"];
    //     $invoiceDueDate = $INPUTS["invoiceDueDate"];
    //     $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
    //     $vendorCode = $INPUTS["vendorCode"];
    //     $vendorName = $INPUTS["vendorName"];
    //     $vendorGstin = $INPUTS["vendorGstin"];
    //     $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
    //     $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
    //     $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
    //     $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
    //     $totalInvoiceTotal = $INPUTS["totalInvoiceTotal"];
    //     $grnItemCode = $INPUTS["grnItemCode"];
    //     $grnItemHsn = $INPUTS["grnItemHsn"];
    //     $grnItemName = $INPUTS["grnItemName"];
    //     $grnItemQty = $INPUTS["grnItemQty"];
    //     $grnItemTax = $INPUTS["grnItemTax"];
    //     $grnItemUnitPrice = $INPUTS["grnItemUnitPrice"];
    //     $grnItemTotalPrice = $INPUTS["grnItemTotalPrice"];
    //     $grnItemReceivedQty = $INPUTS["grnItemReceivedQty"];
    //     $grnApprovedStatus="pending";
    //     if($grnItemReceivedQty==$grnItemQty){
    //         $isCompanyPoEnabled = $this->getCompanyDetails()["isPoEnabled"] ?? "";
    //         if($isCompanyPoEnabled){
    //             if($grnPoNumber!=""){
    //                 $grnApprovedStatus = "approved";
    //             }
    //         }else{
    //             $grnApprovedStatus = "approved";
    //         }
    //     }else{
    //         $grnApprovedStatus="pending";
    //     }
    //     $inserGrnObj = queryInsert('INSERT INTO `'.ERP_GRN.'` SET 
    //                         `companyId`="'.$company_id.'",
    //                         `branchId`="'.$branch_id.'",
    //                         `locationId`="'.$location_id.'",
    //                         `functionalAreaId`="",
    //                         `grnCode`="'.$grnCode.'",
    //                         `grnPoNumber`="'.$grnPoNumber.'",
    //                         `vendorId`="",
    //                         `vendorCode`="'.$vendorCode.'",
    //                         `vendorGstin`="'.$vendorGstin.'",
    //                         `vendorName`="'.$vendorName.'",
    //                         `vendorDocumentNo`="'.$documentNo.'",
    //                         `vendorDocumentDate`="'.$documentDate.'",
    //                         `postingDate`="'. $invoicePostingDate.'",
    //                         `dueDate`="'.$invoiceDueDate.'",
    //                         `dueDays`="'.$invoiceDueDays.'",
    //                         `grnSubTotal`="'.$totalInvoiceSubTotal.'",
    //                         `grnTotalCgst`="'.$totalInvoiceCGST.'",
    //                         `grnTotalSgst`="'.$totalInvoiceSGST.'",
    //                         `grnTotalIgst`="'.$totalInvoiceIGST.'",
    //                         `grnTotalAmount`="'.$totalInvoiceTotal.'",
    //                         `grnCreatedBy`="'.$created_by.'",
    //                         `grnUpdatedBy`="'.$updated_by.'",
    //                         `grnApprovedStatus`="'.$grnApprovedStatus.'"');
    //     if($inserGrnObj["status"]!="success"){
    //         return $inserGrnObj;
    //     }else{
    //         $grnId = $inserGrnObj["insertedId"];
    //         $noItem = count($grnItemCode);   
    //         $errorsInGrnItemsAdd = 0;
    //         $sqls = "";
    //         // insert all GRN items
    //         for($itemKey = 0; $itemKey < $noItem; $itemKey++){
    //             $oneItemCode = $grnItemCode[$itemKey];
    //             $oneItemHsn = $grnItemHsn[$itemKey];
    //             $oneItemName = $grnItemName[$itemKey];
    //             $oneItemQty = $grnItemQty[$itemKey];
    //             $oneItemTax = $grnItemTax[$itemKey];
    //             $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
    //             $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
    //             $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];
    //             $oneItemInsertQuery = 'INSERT INTO `'.ERP_GRN_GOODS.'` SET `grnId`="'.$grnId.'",`grnCode`="'.$grnCode.'",`goodName`="'.$oneItemName.'",`goodDesc`="",`goodCode`="'.$oneItemCode.'",`goodHsn`="'.$oneItemHsn.'",`goodQty`="'.$oneItemQty.'",`receivedQty`="'.$oneItemReceivedQty.'",`unitPrice`="'.$oneItemUnitPrice.'",`totalAmount`="'.$oneItemTotalPrice.'",`grnGoodCreatedBy`="'.$created_by.'",`grnGoodUpdatedBy`="'.$updated_by.'"';
    //             $sqls.=" => ".$oneItemInsertQuery;
    //             $oneItemInsertObj = queryInsert($oneItemInsertQuery);
    //             if($oneItemInsertObj["status"]!="success"){
    //                 $errorsInGrnItemsAdd++;
    //             }
    //         }
    //         // Row material stocks entry
    //         $errorsInGrnIndivisualItemStockAdd = 0;  
    //         $rmStockStatus = ($grnApprovedStatus=="approved") ? "active" : "pending";
    //         if($errorsInGrnItemsAdd==0){
    //             for($itemKey = 0; $itemKey < $noItem; $itemKey++){
    //                 $oneItemCode = $grnItemCode[$itemKey];
    //                 $oneItemHsn = $grnItemHsn[$itemKey];
    //                 $oneItemName = $grnItemName[$itemKey];
    //                 $oneItemQty = $grnItemQty[$itemKey];
    //                 $oneItemTax = $grnItemTax[$itemKey];
    //                 $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
    //                 $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
    //                 $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];
    //                 //INSERT INTO `erp_inventory_stocks_summary` SET `company_id`="'.$company_id.'",`branch_id`="'.$branch_id.'",`location_id`="'.$location_id.'",`itemId`='[value-5]',`itemOpenStocks`='[value-6]',`itemBlockStocks`='[value-7]',`movingWeightedPrice`='[value-8]',`bomStatus`='[value-9]',`createdAt`="'.$created_at.'",`createdBy`="'.$created_by.'",`updatedAt`="'.$updated_at.'",`updatedBy`="'.$updated_by.'";
    //                 // $oneItemRmStockObj = queryInsert('INSERT INTO `'.ERP_RM_STOCKS.'` SET `itemCode`="'.$oneItemCode.'",`purchaseOrderNo`="'.$grnPoNumber.'",`productionOrderNo`="",`lotNo`="",`batchNo`="",`storageLocation`="",`itemQuantity`="'.$oneItemReceivedQty.'",`baseUnit`="",`itemPrice`="'.$oneItemTotalPrice.'",`stockStatus`="open", `createdBy`="'.$created_by.'",`updatedBy`="'.$updated_by.'", `rmStockStatus`="'.$rmStockStatus.'"');            
    //                 // if($oneItemRmStockObj["status"]!="success"){
    //                 //     $errorsInGrnIndivisualItemStockAdd++;
    //                 // }
    //             }
    //         }
    //         // Update Row material stocks
    //         $errorsInGrnIndivisualItemStockUpdate = 0;
    //         if($errorsInGrnIndivisualItemStockAdd==0 && $grnApprovedStatus=="approved"){
    //             for($itemKey = 0; $itemKey < $noItem; $itemKey++){
    //                 $oneItemCode = $grnItemCode[$itemKey];
    //                 $oneItemHsn = $grnItemHsn[$itemKey];
    //                 $oneItemName = $grnItemName[$itemKey];
    //                 $oneItemQty = $grnItemQty[$itemKey];
    //                 $oneItemTax = $grnItemTax[$itemKey];
    //                 $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
    //                 $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
    //                 $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];
    //                 $updateItemRmStockObj = queryUpdate('UPDATE `'.ERP_INVENTORY_ITEMS.'` SET `itemOpenStocks`=(`itemOpenStocks`+'.$oneItemReceivedQty.') WHERE `itemCode`="'.$oneItemCode.'"');
    //                 if($updateItemRmStockObj["status"]!="success"){
    //                     $errorsInGrnIndivisualItemStockUpdate++;
    //                 }
    //             }
    //         }
    //         if($errorsInGrnItemsAdd == 0 && $errorsInGrnIndivisualItemStockAdd == 0 && $errorsInGrnIndivisualItemStockUpdate==0){
    //             return [
    //                 "status"=> "success",
    //                 "message"=> ($grnApprovedStatus=="pending")?"GRN posted successfully, waiting for 'approval'":"GRN posted successfully."
    //             ];
    //         }else{
    //             return [
    //                 "status"=> "warning",
    //                 "message"=> "GRN posted failed, try again!",
    //                 "errorsInGrnItemsAdd"=>$errorsInGrnItemsAdd,
    //                 "errorsInGrnIndivisualItemStockAdd"=>$errorsInGrnIndivisualItemStockAdd,
    //                 "errorsInGrnIndivisualItemStockUpdate"=>$errorsInGrnIndivisualItemStockUpdate
    //             ];
    //         }
    //     }
    // }

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
    function getGrnList()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        return queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnStatus`!="deleted" AND `iv_status`="0" ORDER BY grnId DESC', true);
    }

    function getGrnIVPostedList()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        return queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnStatus`!="deleted" AND `iv_status`="1" ORDER BY grnId DESC', true);
    }

    function getPendingGrnList()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        return queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND status = '0' AND grn_active_status = 'active' ORDER BY grn_mul_id DESC", true);
    }

    function getPostedGrnList()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        return queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND status = '1' AND grn_active_status = 'active' ORDER BY grn_mul_id DESC", true);
    }

    function checkAllGoods($array)
    {
        // Extracting the values of 'itemInvoiceGoodsType' from all arrays
        $goodsTypes = array_column($array, 'itemInvoiceGoodsType');

        // If there's only one unique value and it's 'goods', return true
        return count(array_unique($goodsTypes)) === 1 && $goodsTypes[0] === 'service';
    }

    function getPoAndGrnId($data)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $pogrnid=[];

        foreach ($data as $id) {
            if (preg_match('/^PO/', $id)) {
                $poId = queryGet("SELECT `po_id` FROM `erp_branch_purchase_order` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `po_number`='" . $id . "'", false)["data"]["po_id"];               
                $pogrnid[] = "PO-" . $poId;
            } elseif (preg_match('/^GRN/', $id)) {
                $grnId = queryGet("SELECT `grnId` FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `branchId`='" . $branch_id . "' AND `locationId`='" . $location_id . "' AND `grnCode`='" . $id . "'", false)["data"]["grnId"];
                $pogrnid[] = "GRN-" . $grnId;
            }
        }
        $output_string = implode('|', $pogrnid);
        return $output_string;

    }
    function createManualGrn($INPUTS)
    {
        // console($INPUTS);
        // exit();

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $isQaEnabled;
        $returnData = [];
        global $companyCountry;
        //   $in = $INPUTS["grnItemList"];

        //   foreach($in as $data){
        //     foreach($data['cost_center'] as $costcenter){
        //         console($costcenter);


        //     }
        //   }

        // console($INPUTS["invoicePoNumber"]);
        // echo gettype($INPUTS["invoicePoNumber"]);
        // echo implode(",", $INPUTS["invoicePoNumber"]);


        // $grnItemList = $INPUTS["grnItemList"];
        // console($grnItemList);

        // foreach ($grnItemList as $vendor_id => $allItems) {

        //     echo "-----------------------itemUnitPrice <br>";
        // echo $allItemUnitPrice = array_sum(array_column($allItems, 'itemUnitPrice'));
        //     echo " <br> -----------------------itemReceivedQty";
        // echo $allItemQty = array_sum(array_column($allItems, 'itemReceivedQty'));
        //     echo " <br> -----------------------allSubTotal";
        // echo $allSubTotal = $allItemUnitPrice * $allItemQty;
        //     echo " <br> -----------------------itemCGST";
        // echo $totalInvoiceCGST = array_sum(array_column($allItems, 'itemCGST'));
        //     echo " <br> -----------------------itemSGST";
        // echo $totalInvoiceSGST = array_sum(array_column($allItems, 'itemSGST'));
        //     echo " <br> -----------------------itemIGST";
        // echo $totalInvoiceIGST = array_sum(array_column($allItems, 'itemIGST'));
        //     echo " <br> -----------------------itemInvoiceTDSValue";
        // echo $totalTds = array_sum(array_column($allItems, 'itemInvoiceTDSValue'));
        //     echo " <br> -----------------------totalInvoiceTotal";

        // echo $totalInvoiceTotal = $allSubTotal + $totalInvoiceCGST + $totalInvoiceSGST + $totalInvoiceIGST - $totalTds;
        // }

        // console($INPUTS);
        // dd();
        //  exit();

        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "grnItemList" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputs";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $po_date = $INPUTS["po_date"] ?? "";
        $po_date = $INPUTS["invoicePostingDate"] ?? "";
        // ["PO12345","PO12345"]
        $grnPoNumberString = json_decode($INPUTS["invoicePoNumber"], true);
        $grnPoNumber = implode('|', $grnPoNumberString);

        $poAndGrnId = $this->getPoAndGrnId($grnPoNumberString);
        // console($poAndGrnId);
        // exit();
        $invoicePostingDate = $INPUTS["invoicePostingDate"];

        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        // $vendorId = $INPUTS["vendorId"];
        // $vendorCode = $INPUTS["vendorCode"];
        // $vendorName = addslashes($INPUTS["vendorName"]);
        // $vendorGstin = $INPUTS["vendorGstin"] ?? '';
        // $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
        // $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
        // $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
        // $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];

        $locationGstinStateName = $INPUTS["locationGstinStateName"];

        // $vendorDocumentFile = $uploadedInvoiceName;


        $grnItemList = $INPUTS["grnItemList"];
        $multiple_id = $INPUTS["id"];
        $currency = $INPUTS['currency'];
        $rate = $INPUTS['currency_conversion_rate'];
        if (isset($INPUTS['funcArea'])) {
            $func = $INPUTS['funcArea'];
        } else {
            $func = 0;
        }

        $extra_remark = $INPUTS['extra_remark'] ?? '';


        $grnApprovedStatus = "approved";


        // $check_invoice_exists = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `vendorDocumentNo` = '" . $documentNo . "' AND `vendorId`='" . $vendorId . "' AND `grnStatus`='active'");
        // $check_posted_grn = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $branchDeails["company_id"] . "' AND `vendorDocumentNo` = '" . $inv_no . "'");

        // if ($check_invoice_exists["numRows"] != 0) {
        //     $returnData['status'] = "warning";
        //     $returnData['message'] = "This Invoice already exists";
        //     $returnData['errors'] = "";
        //     return $returnData;
        // }
        $errorsItemStockLogInsert = 0;
        $errorsInGrnItemsAdd = 0;
        $errorsItemStockSummaryUpdate = 0;
        $errorGrnSubmit = 0;
        $accerror = 0;
        $allSubTotal = 0;
        $errorArray = [];
        // Posted GRN ITEMS allocated Price basis MWP change
        if (!empty($INPUTS["grnItemPostedList"]) && is_array($INPUTS["grnItemPostedList"])) {
            $grnItemPostedList = $INPUTS["grnItemPostedList"];

            foreach ($grnItemPostedList as $grnItem) {
                $allocateCost = $grnItem["allocatedCost"] ?? 0;

                if ($allocateCost > 0) {
                   
                    $oneItemId = $grnItem["itemId"] ?? 0;
                    $grnno = $grnItem["grnno"] ?? '';
                    $oneItemCode = $grnItem["itemCode"] ?? '';
                    $oneItemGoodsType = $grnItem["itemInvoiceGoodsType"] ?? '';
                    $oneQty = $grnItem["itemReceivedQty"] ?? 0;
                    $oneItemAllocatedPricee = $grnItem['allocatedCost'];

                    if ($oneItemGoodsType === "goods" && $oneItemId && $grnno) {
                        $grnItems = queryGet("
                            SELECT * FROM `erp_inventory_stocks_log`
                            WHERE `refActivityName` = 'GRN'
                              AND `itemId` = {$oneItemId}
                              AND `refNumber` = '{$grnno}'
                              AND `companyId` = {$company_id}
                              AND `locationId` = {$location_id}
                              AND `branchId` = {$branch_id}
                            ")['data'] ?? [];
                        if (!empty($grnItems) && is_array($grnItems)) {
                            $itemId = $grnItems["itemId"];
                            $storageType = $grnItems["storageType"];
                            $storageLocationId = $grnItems["storageLocationId"];
                            $itemQty = -1 * ($grnItems["itemQty"] ?? 0);
                            $itemPrice = $grnItems["itemPrice"];
                            $logRef = $grnItems["logRef"];
                            $bornDate = $grnItems["bornDate"];
                            $itemUom = $grnItems["itemUom"];
                            $reverseRefCode = "REV" . ($grnItems["refNumber"] ?? '');
                            $newitemprice = $itemPrice + ($oneItemAllocatedPricee / $oneQty);

                            $stc_out = queryInsert("
                                INSERT INTO `erp_inventory_stocks_log` SET 
                                    `companyId` = {$company_id},
                                    `branchId` = {$branch_id},
                                    `locationId` = {$location_id},
                                    `storageLocationId` = {$storageLocationId},
                                    `storageType` = '{$storageType}',
                                    `itemId` = {$itemId},
                                    `itemQty` = {$itemQty},
                                    `itemUom` = '{$itemUom}',
                                    `itemPrice` = {$itemPrice},
                                    `refActivityName` = 'REV-GRN',
                                    `logRef` = '{$logRef}',
                                    `refNumber` = '{$reverseRefCode}',
                                    `bornDate` = '{$bornDate}',
                                    `postingDate` = '{$bornDate}',
                                    `createdBy` = '{$created_by}',
                                    `updatedBy` = '{$updated_by}'
                            ");
                            if ($stc_out['status'] == "success") {
                                calculateReverseMwp($itemId, $oneQty, $itemPrice, 'grnrev');
                            }


                            $stc_in = queryInsert("
                                INSERT INTO `erp_inventory_stocks_log` SET 
                                    `companyId` = {$company_id},
                                    `branchId` = {$branch_id},
                                    `locationId` = {$location_id},
                                    `storageLocationId` = {$storageLocationId},
                                    `storageType` = '{$storageType}',
                                    `itemId` = {$itemId},
                                    `itemQty` = {$oneQty},
                                    `itemUom` = '{$itemUom}',
                                    `itemPrice` = {$newitemprice},
                                    `refActivityName` = 'GRN',
                                    `logRef` = '{$logRef}',
                                    `refNumber` = '{$grnno}',
                                    `bornDate` = '{$bornDate}',
                                    `postingDate` = '{$bornDate}',
                                    `createdBy` = '{$created_by}',
                                    `updatedBy` = '{$updated_by}'
                            ");

                            if ($stc_in['status'] == "success") {
                                $mwp = calculateNewMwp($itemId, $oneQty, $newitemprice, "GRN");
                                queryUpdate('UPDATE `erp_grn_goods` SET `allocated_cost`=' . $oneItemAllocatedPricee . ' WHERE `grnCode`="' . $grnno . '" AND `goodId`="'.$itemId.'"');
                            }
                        }
                    }
                }
            }
        }
        foreach ($grnItemList as $vendor_id => $allItems) {

            $documentNo = $INPUTS["documentNo"][$vendor_id];
            $documentDate = $INPUTS["documentDate"][$vendor_id];
            $invoiceDueDate = $INPUTS["invoiceDueDate"][$vendor_id];

            if ($this->checkAllGoods($allItems)) {
                echo "SRN";
                $grnNo = "SRN" . time() . rand(100, 999);
                $grnType = "srn";
                $remarks = "SRN By PO " . $grnNo . " " . $extra_remark;
            } else {
                echo "GRN";
                $grnNo = "GRN" . time() . rand(100, 999);
                $grnType = "grn";
                $remarks = "GRN By PO " . $grnNo . " " . $extra_remark;
            }
            if (isset($_FILES["invoice_file_name"]) && $_FILES["invoice_file_name"] != "") {
                $file_tmpname = $_FILES["invoice_file_name"]["tmp_name"][$vendor_id];
                $file_name = $_FILES["invoice_file_name"]['name'][$vendor_id];
                $file_size = $_FILES["invoice_file_name"]['size'][$vendor_id];

                $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
                $maxsize = 2 * 1024 * 1024; // 10 MB

                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $customFileName = $grnNo.'_' . time() . rand(100, 999)  . '.' . $ext;

                // $uploadedInvoiceObj = uploadFileS3(["name" => $customFileName, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);
                // if ($uploadedInvoiceObj["status"] == "success") {
                //     $uploadedInvoiceName = basename($uploadedInvoiceObj["data"]['key']);
                // } else {
                //     $uploadedInvoiceName = "";
                // }
                $uploadedInvoiceObj = uploadFile(["name" => $customFileName, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);

                if ($uploadedInvoiceObj["status"] == "success") {
                    $uploadedInvoiceName = $uploadedInvoiceObj["data"];
                } else {
                    $uploadedInvoiceName = "";
                }
            } else {
                $uploadedInvoiceName = null;
            }

            $vendorDocumentFile = $uploadedInvoiceName;

            

            $grnCode = $grnNo;

            $vendor_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id`='" . $company_id . "' AND `vendor_id` = '" . $vendor_id . "'", false);
            $vendor_code = $vendor_details["data"]["vendor_code"];
            $vendor_gst = $vendor_details["data"]["vendor_gstin"];
            $vendor_name = $vendor_details["data"]["trade_name"];

            $sumTaxAmount = 0;
            $allItemUnitPrice = array_sum(array_column($allItems, 'itemUnitPrice'));
            $allItemQty = array_sum(array_column($allItems, 'itemReceivedQty'));
            if ($companyCountry == 103) {
                $allSubTotal = array_sum(array_column($allItems, 'itemTotalPrice'));
                $totalInvoiceCGST = array_sum(array_column($allItems, 'itemCGST'));
                $totalInvoiceSGST = array_sum(array_column($allItems, 'itemSGST'));
                $totalInvoiceIGST = array_sum(array_column($allItems, 'itemIGST'));
                $totalTds = array_sum(array_column($allItems, 'itemInvoiceTDSValue'));
                $taxComponents = json_encode($this->calculateTotalTax($allItems));
            } else {
                $allSubTotal = array_sum(array_column($allItems, 'itemTotalPrice'));
                $totalTds = array_sum(array_column($allItems, 'itemInvoiceTDSValue'));



                $taxComponents = json_encode($this->calculateTotalTax($allItems));
                $datasum = json_decode($taxComponents, true);


                $array = json_decode($datasum, true);

                $taxTrailArray = [];

                if (!empty($array)) {
                    foreach ($array as $tax) {
                        $taxTrailArray[$tax['gstType']] = decimalValuePreview($tax['taxAmount']);
                    }
                    $gstTrailFlag = 1;
                } else {
                    $gstTrailFlag = 0;
                }
                // Sum taxAmount
                $sumTaxAmount = array_sum(array_column($array, 'taxAmount'));


                $totalInvoiceCGST = 0;
                $totalInvoiceSGST = 0;
                $totalInvoiceIGST = 0;
            }

            $totalInvoiceTotal = $allSubTotal + $totalInvoiceCGST + $totalInvoiceSGST + $totalInvoiceIGST - $totalTds + $sumTaxAmount;

            if ($vendor_gst == "" || $vendor_gst == NULL || !isset($vendor_gst)) {
                $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
            } else {
                $vendorGstinStateCode = substr($vendor_gst, 0, 2);
            }


            $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
            if ($companyCountry != 103) {
                $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendor_id AND `vendor_business_primary_flag` = 1");
                $b_row = $b_places['data'];
                $vendorGstinStateName = $b_row['vendor_business_state'];
                $venderabn = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendor_id");

                $abn = $venderabn['data'];
                $vendor_gst = $abn['vendor_gstin'];
            }
            $insertgrnSql = 'INSERT INTO `' . ERP_GRN . '` SET 
                            `companyId`="' . $company_id . '",
                            `branchId`="' . $branch_id . '",
                            `locationId`="' . $location_id . '",
                            `pending_grn_id`="' . $multiple_id . '",
                            `functionalAreaId`="",
                            `grnCode`="' . $grnNo . '",
                            `grnType`="' . $grnType . '",
                            `grnPoNumber`="' . $grnPoNumber . '",
                            `po_date`="' . $po_date . '",
                            `vendorId`=' . $vendor_id . ',
                            `vendorCode`="' . $vendor_code . '",
                            `vendorGstin`="' . $vendor_gst . '",
                            `vendorName`="' . $vendor_name . '",
                            `vendorDocumentNo`="' . $documentNo . '",
                            `vendorDocumentDate`="' . $documentDate . '",
                            `postingDate`="' . $invoicePostingDate . '",
                            `dueDate`="' . $invoiceDueDate . '",
                            `dueDays`="' . $invoiceDueDays . '",
                            `paymentStatus`="1",
                            `dueAmt`="' . $totalInvoiceTotal . '",
                            `grnSubTotal`="' . $allSubTotal . '",
                            `grnTotalCgst`="' . $totalInvoiceCGST . '",
                            `grnTotalSgst`="' . $totalInvoiceSGST . '",
                            `grnTotalIgst`="' . $totalInvoiceIGST . '",
                            `grnTotalTds`="' . $totalTds . '",
                            `taxComponents`=' . $taxComponents . ',
                            `grnTotalAmount`="' . $totalInvoiceTotal . '",
                            `locationGstinStateName`="' . $locationGstinStateName . '",
                            `vendorGstinStateName`="' . $vendorGstinStateName . '",
                            `vendorDocumentFile`="' . $vendorDocumentFile . '",
                            `grnCreatedBy`="' . $created_by . '",
                            `grnUpdatedBy`="' . $updated_by . '",
                            `currency` = "' . $currency . '",
                            `conversion_rate` = "' . $rate . '",
                            `functional_area` = "' . $func . '" ,
                            `grnApprovedStatus`="' . $grnApprovedStatus . '"';

            $inserGrnObj = queryInsert($insertgrnSql);

            if ($inserGrnObj["status"] != "success") {
                $errorGrnSubmit++;
            } else {

                $grnId = $inserGrnObj["insertedId"];

                //Enter PO ID in relation table

                $splitted_po = explode("|", $grnPoNumber);

                foreach ($splitted_po as $each_po) {

                    $check_po = substr($each_po, 0, 2);

                    if ($check_po == "PO") {

                        $poId = queryGet("SELECT `po_id` FROM `erp_branch_purchase_order` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `po_number`='" . $each_po . "'", false)["data"]["po_id"] ?? 0;

                        $relationSql = 'INSERT INTO `erp_grn_po_relation` SET 
                                `grn_id`="' . $grnId . '",
                                `po_id`="' . $poId . '",
                                `po_number`="' . $each_po . '",
                                `grn_number`="' . $grnNo . '"';

                        $relationObj = queryInsert($relationSql);
                    }
                }


                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_GRN;
                $auditTrail['basicDetail']['column_name'] = 'grnId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $grnId;  // primary key
                $auditTrail['basicDetail']['document_number'] = $grnCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_type'] = 'vendor';
                $auditTrail['basicDetail']['party_id'] = $vendor_id;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = $grnType . ' Creation';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insertgrnSql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Basic Details']['GRN_Code'] = $grnCode;
                $auditTrail['action_data']['Basic Details']['GRN_Type'] = $grnType;
                $auditTrail['action_data']['Basic Details']['GRN PO Number'] = $grnPoNumber;
                $auditTrail['action_data']['Basic Details']['PO_Date'] = formatDateWeb($po_date);
                $auditTrail['action_data']['Basic Details']['Vendor_Code'] = $vendor_code;
                $auditTrail['action_data']['Basic Details']['Vendor_Gstin'] = $vendor_gst;
                $auditTrail['action_data']['Basic Details']['Vendor_Name'] = $vendor_name;
                $auditTrail['action_data']['Basic Details']['Vendor Document No'] = $documentNo;
                $auditTrail['action_data']['Basic Details']['Vendor Document Date'] = formatDateORDateTime($documentDate);
                $auditTrail['action_data']['Basic Details']['Posting_Date'] = formatDateORDateTime($invoicePostingDate);
                $auditTrail['action_data']['Basic Details']['Due_Date'] = formatDateORDateTime($invoiceDueDate);
                $auditTrail['action_data']['Basic Details']['Due_Days'] = $invoiceDueDays;
                $auditTrail['action_data']['Basic Details']['Payment_Status'] = "1";
                $auditTrail['action_data']['Basic Details']['Due_Amt'] = decimalValuePreview($totalInvoiceTotal);
                $auditTrail['action_data']['Basic Details']['GRN Sub Total'] = decimalValuePreview($allSubTotal);

                if ($gstTrailFlag == 1) {
                    foreach ($taxTrailArray as $taxType => $amount) {
                        $auditTrail['action_data']['Basic Details']["GRN Total $taxType"] = decimalValuePreview($amount);
                    }
                } else {
                    $auditTrail['action_data']['Basic Details']['GRN Total Cgst'] = decimalValuePreview($totalInvoiceCGST);
                    $auditTrail['action_data']['Basic Details']['GRN Total Sgst'] = decimalValuePreview($totalInvoiceSGST);
                    $auditTrail['action_data']['Basic Details']['GRN Total Igst'] = decimalValuePreview($totalInvoiceIGST);
                }

                $auditTrail['action_data']['Basic Details']['GRN Total TDS'] = decimalValuePreview($totalTds);
                $auditTrail['action_data']['Basic Details']['GRN Total Amount'] = decimalValuePreview($totalInvoiceTotal);
                $auditTrail['action_data']['Basic Details']['Location Gstin State Name'] = $locationGstinStateName;
                $auditTrail['action_data']['Basic Details']['Vendor Gstin State Name'] = $vendorGstinStateName;
                $auditTrail['action_data']['Basic Details']['Vendor Document File'] = $vendorDocumentFile;
                $auditTrail['action_data']['Basic Details']['GRN Created By'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Basic Details']['GRN Updated By'] = getCreatedByUser($updated_by);
                $auditTrail['action_data']['Basic Details']['Currency'] = getSingleCurrencyType($currency);
                $auditTrail['action_data']['Basic Details']['Conversion_Rate'] = $rate;
                $auditTrail['action_data']['Basic Details']['Functional_Area'] = $func;
                $auditTrail['action_data']['Basic Details']['GRN Approved Status'] = $grnApprovedStatus;


                $grnItemListAcc = [];
                $postatusFlug = 0;
                $poCloseArray=[];
                foreach ($allItems as $oneItemLineId => $grnItem) {
                    $oneItemId = $grnItem["itemId"] ?? 0;
                    $oneItemCode = $grnItem["itemCode"];
                    $oneItemHsn = $grnItem["itemHsn"];
                    $oneItemName = addslashes($grnItem["itemName"]);
                    $oneItemQty = $grnItem["itemQty"];
                    $oneItemReceiveQty = $grnItem["itemReceivedQty"];
                    $oneItemTax = $grnItem["itemTax"];
                    $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                    $oneItemSubTotalPrice = $oneItemUnitPrice * $oneItemReceiveQty;
                    $oneItemTotalPrice = $grnItem["itemGrandTotalPrice"];
                    $oneItemAllocatedPrice = $grnItem['allocatedCost'];
                    if ($grnItem["itemStorageLocationId"] == "inventorise_" . $oneItemLineId) {
                        $oneItemStorageLocationId = 0;
                    } else {
                        $oneItemStorageLocationId = $grnItem["itemStorageLocationId"] ?? 0;
                    }

                    $oneItemStocksQty = $grnItem["itemStockQty"] ?? 0;
                    $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                    $oneItemUOM = $grnItem["itemUOM"] ?? "";
                    ///////////////////////////////////////////////////////////
                    $oneItemCGST = $grnItem["itemCGST"] ?? "";
                    $oneItemSGST = $grnItem["itemSGST"] ?? "";
                    $oneItemIGST = $grnItem["itemIGST"] ?? "";
                    $oneItemTDS = $grnItem["itemTds"] ?? "";
                    $taxComponentsitem = $grnItem["hiddenTaxValues"] ?? "";
                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";
                    $oneItemRemainQty = $grnItem["itemRemainQty"] ?? 0;
                    $onePoItemId = $grnItem["PoItemId"] ?? 0;
                    $PoNumber = $grnItem["itemPurchaseOrder"] ?? 0;
                    $allocated_array = $grnItem["allocated_array"] ?? "";

                    $oneItemInsertQuery = "INSERT INTO `" . ERP_GRN_GOODS . "` 
                                            SET `grnId`='" . $grnId . "',
                                                `grnCode`='" . $grnCode . "',
                                                `goodName`='" . $oneItemName . "',
                                                `goodDesc`='',
                                                `goodId`='" . $oneItemId . "',
                                                `goodCode`='" . $oneItemCode . "',
                                                `goodHsn`='" . $oneItemHsn . "',
                                                `goodQty`='" . $oneItemReceivedQty . "',
                                                `receivedQty`='" . $oneItemReceivedQty . "',
                                                `unitPrice`='" . $oneItemUnitPrice . "',
                                                `cgst`='" . $oneItemCGST . "', 
                                                `sgst`='" . $oneItemSGST . "',
                                                `igst`='" . $oneItemIGST . "',
                                                `tds`='" . $oneItemTDS . "',
                                                `taxComponents`='" . $taxComponentsitem . "',
                                                `goodstype`='" . $oneItemgoodsType . "',
                                                `totalAmount`='" . $oneItemTotalPrice . "',
                                                `itemStocksQty`='" . $oneItemReceivedQty . "',
                                                `itemUOM`='" . $oneItemUOM . "', 
                                                `itemStorageLocation`=" . $oneItemStorageLocationId . ",
                                                `grnType`='" . $grnType . "', 
                                                `allocated_array`='" . $allocated_array . "',
                                                `allocated_cost`='" . $oneItemAllocatedPrice ."',
                                                `grnGoodCreatedBy`='" . $created_by . "',
                                                `grnGoodUpdatedBy`='" . $updated_by . "'";

                    $oneItemInsertObj = queryInsert($oneItemInsertQuery);
                    // console($oneItemInsertObj);

                    array_push($errorArray, $oneItemInsertObj);

                    if ($oneItemInsertObj["status"] != "success") {
                        $errorsInGrnItemsAdd++;
                    } else {

                        $lastgrnitem_id = $oneItemInsertObj['insertedId'];
                        // if ($oneItemgoodsType == "service") {
                        //     foreach ($grnItem['cost_center'] as $costcenter) {
                        //         $costcenter = $costcenter['name'];
                        //         $costcenter_rate = $costcenter['rate'];
                        //         $costcenter_id = $costcenter['id'];
                        //         $cost_center = queryInsert("INSERT INTO `erp_srn_costcenter_details` SET `srn_id` = $grnId, `costcenter_id`= $costcenter_id, `costcenter_percentage`=$costcenter_rate ,`srn_item_id`=$lastgrnitem_id");
                        //     }
                        // }
                        queryUpdate('UPDATE `erp_branch_purchase_order_items` SET `remainingQty`=' . $oneItemRemainQty . ' WHERE `po_item_id`="' . $onePoItemId . '"');

                        $po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` WHERE remainingQty > 0 AND po_item_id = '" . $onePoItemId . "'", true);

                        if ($po_item["numRows"] > 0) {
                            $postatusFlug++;
                        }else{
                            $poCloseArray[$PoNumber]=[
                                'status'=>$postatusFlug,
                            ];
                        }

                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN_Code'] = $grnCode;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Name'] = $oneItemName;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Desc'] = "";
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Code'] = $oneItemCode;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_HSN'] = $oneItemHsn;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Qty'] = decimalQuantityPreview($oneItemQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Received_Qty'] = decimalQuantityPreview($oneItemReceivedQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Unit_Price'] = decimalValuePreview($oneItemUnitPrice);

                        if ($companyCountry == 103) {
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['CGST'] = decimalValuePreview($oneItemCGST);
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['SGST'] = decimalValuePreview($oneItemSGST);
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['IGST'] = decimalValuePreview($oneItemIGST);
                        } else {
                            $taxComponentsItem = $grnItem["hiddenTaxValues"] ?? "";
                            $taxArrayItem = json_decode($taxComponentsItem, true);

                            if (!empty($taxArrayItem)) {
                                foreach ($taxArrayItem as $itemTax) {
                                    $taxType = $itemTax['gstType'] ?? '';
                                    $taxAmt = decimalValuePreview($itemTax['taxAmount'] ?? 0);
                                    if ($taxType != '') {
                                        $auditTrail['action_data']['Goods Details'][$oneItemCode]["$taxType"] = $taxAmt;
                                    }
                                }
                            }
                        }

                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['TDS'] = decimalValuePreview($oneItemTDS);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Total_Amount'] = decimalValuePreview($oneItemTotalPrice);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Item Stocks Qty'] = decimalQuantityPreview($oneItemStocksQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Item_UOM'] = $oneItemUOM;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN_Type'] = $grnType;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN Good Created By'] = getCreatedByUser($created_by);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN Good Updated By'] = getCreatedByUser($updated_by);

                    }


                    $allocatedArray = json_decode($grnItem['allocated_array'], true);

                    if (isset($allocatedArray) && count($allocatedArray) > 0) {
                        foreach ($allocatedArray as $key => $value) {
                            $from_item_id = $value["formItemId"];
                            $to_item_id = $value["toItemId"];
                            $allocated_cost = $value["allocatedCost"];
                            $to_vendor_id = $value["toVendorId"];
                            $alloItem_item = queryGet("SELECT parentGlId,itemCode,itemName FROM `erp_inventory_items` WHERE itemId = $to_item_id")['data'];
                            $alloItemName = $alloItem_item['itemName'];
                            $alloItemCode = $alloItem_item['itemCode'];
                            $alloItemGl = $alloItem_item['parentGlId'];


                            $allocInsertSql = 'INSERT INTO `erp_cost_allocation` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`grn_code`="' . $grnCode . '",`grn_id`=' . $grnId . ', `from_item_id`=' . $from_item_id . ',`to_item_id`=' . $to_item_id . ',`to_vendor_id`=' . $to_vendor_id . ',`allocated_cost`=' . $allocated_cost . ', `created_by`="' . $created_by . '"';

                            $allocInsertSqlObj = queryInsert($allocInsertSql);

                            if ($allocInsertSqlObj["status"] != "success") {
                                $errorsItemStockLogInsert++;
                            }
                            mt_srand((int)(microtime(true) * 1000000));
                            $code = mt_rand(1000, 9999);

                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["parentGlId"] = $alloItemGl;
                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["itemCode"] = $alloItemCode;
                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["itemName"] = $alloItemName;
                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["itemTotalPrice"] = $allocated_cost;
                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["remark"] = $alloItemName . ' [' . $alloItemCode . "] Allocate Cost from" . $oneItemName . ' [' . $oneItemCode . ']';
                            $grnItemListAcc[$oneItemLineId . $to_item_id . $code]["type"] = 'service';
                            // console($grnItemListAcc);

                        }
                    } else {

                        $grnItemListAcc[$oneItemLineId]["parentGlId"] = $this->getInventoryItemParentGl($grnItem["itemId"]);
                        $grnItemListAcc[$oneItemLineId]["itemCode"] = $oneItemCode;
                        $grnItemListAcc[$oneItemLineId]["itemName"] = $oneItemName;
                        $grnItemListAcc[$oneItemLineId]["itemTotalPrice"] = $oneItemSubTotalPrice;
                        $grnItemListAcc[$oneItemLineId]["remark"] = $oneItemCode;
                        $grnItemListAcc[$oneItemLineId]["type"] = $oneItemgoodsType;
                    }
                }
                foreach ($poCloseArray as $PoNumber => $po) {
                    if ($po['status'] == 0) {
                        // Update to close
                        $close = 10;
                        queryUpdate('UPDATE `erp_branch_purchase_order` SET `po_status`=' . $close . ' WHERE location_id=' . $location_id . ' AND company_id=' . $company_id . ' AND branch_id=' . $branch_id . ' AND `po_number`="' . $PoNumber . '"');
                    }
                }
                

                // Row material stocks entry 
                // new update on 12-12-2022 18:35pm

                // if ($errorsInGrnItemsAdd == 0) {
                //     foreach ($allItems as $grnItem) {
                //         $oneItemId = $grnItem["itemId"];
                //         $oneItemCode = $grnItem["itemCode"];
                //         $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                //         if ($oneItemgoodsType == "goods") {
                //             $oneItemStocksQty = $grnItem["itemReceivedQty"] ?? 0.00; //500
                //             $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50
                //             $oneItemAllocatedPrice = $grnItem["allocatedCost"] ?? 0.00; //50

                //             $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId);

                //             $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0; //3000.00
                //             $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0; //50.00
                //             $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                //             $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                //             $itemNewTotalPrice = (float)$prevTotalPrice + (($oneItemStocksQty * $oneItemUnitPrice) + $oneItemAllocatedPrice); //(150000 + (500 * 50)) //175000
                //             $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                //             if (is_nan($movingWeightedPrice)) {
                //                 $movingWeightedPrice = 0;
                //             }

                //             if ($goodStockSummaryCheckSql["numRows"] > 0) {

                //                 $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;

                //                 // $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                //                 // if ($checkfgrm["data"]["storage_location_material_type"] == "RM" && $checkfgrm["data"]["storage_location_type"] == "RM-WH") {
                //                 //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `rmWhOpen`=`rmWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                //                 // } elseif ($checkfgrm["data"]["storage_location_material_type"] == "FG" && $checkfgrm["data"]["storage_location_type"] == "FG-WH") {
                //                 //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `fgWhOpen`=`fgWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                //                 // }

                //                 $goodStockInsertObj = queryUpdate($goodStockInserSql);
                //                 if ($goodStockInsertObj["status"] != "success") {
                //                     $errorsItemStockSummaryUpdate++;
                //                     // return $goodStockInserSql;
                //                     //  $goodStockInsertObj;
                //                 }

                //                 queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $movingWeightedPrice . ',`createdBy`="' . $created_by . '"');
                //             } else {

                //                 $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;

                //                 $goodStockInsertObj = queryInsert($goodStockInserSql);

                //                 if ($goodStockInsertObj["status"] != "success") {
                //                     $errorsItemStockSummaryUpdate++;
                //                 } else {
                //                     ///---------------------------------Audit Log Start---------------------
                //                     $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                //                     $auditTrailSummry = array();
                //                     $auditTrailSummry['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                //                     $auditTrailSummry['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                //                     $auditTrailSummry['basicDetail']['column_name'] = 'itemId'; // Primary key column
                //                     $auditTrailSummry['basicDetail']['document_id'] = $oneItemId;  //     primary key
                //                     $auditTrailSummry['basicDetail']['document_number'] = $oneItemCode;
                //                     $auditTrailSummry['basicDetail']['action_code'] = $action_code;
                //                     $auditTrailSummry['basicDetail']['action_referance'] = $grnCode;
                //                     $auditTrailSummry['basicDetail']['action_title'] = 'Item Stock added';  //Action comment
                //                     $auditTrailSummry['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                //                     $auditTrailSummry['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                //                     $auditTrailSummry['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                //                     $auditTrailSummry['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                //                     $auditTrailSummry['basicDetail']['action_sqlQuery'] = base64_encode($goodStockInserSql);
                //                     $auditTrailSummry['basicDetail']['others'] = '';
                //                     $auditTrailSummry['basicDetail']['remark'] = '';



                //                     $auditTrailSummry['action_data']['Summary']['itemTotalQty'] = $itemNewTotalQty;
                //                     $auditTrailSummry['action_data']['Summary']['movingWeightedPrice'] = $movingWeightedPrice;
                //                     $auditTrailSummry['action_data']['Summary']['rmWhOpen'] = $oneItemStocksQty;

                //                     $auditTrailreturn = generateAuditTrail($auditTrailSummry);
                //                 }

                //                 queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $oneItemUnitPrice . ',`createdBy`="' . $created_by . '"');
                //             }
                //         }
                //     }
                // }

                // Row material stocks entry log entry

                if ($errorsItemStockSummaryUpdate == 0) {

                    foreach ($allItems as $grnItem) {
                        $oneItemId = $grnItem["itemId"];
                        $oneItemCode = $grnItem["itemCode"];
                        $oneItemHsn = $grnItem["itemHsn"];
                        $oneItemName = addslashes($grnItem["itemName"]);
                        $oneItemQty = $grnItem["itemQty"];
                        $oneItemUomId = $grnItem["itemUOMID"] ?? "";
                        $oneItemTax = $grnItem["itemTax"];
                        $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                        $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                        $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                        $oneItemStocksQty = $grnItem["itemStockQty"] ?? $oneItemQty;
                        $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                        $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";
                        $oneItemAllocatedPrice = $grnItem["allocatedCost"] ?? 0.00; //50
                        $oneItemUnitPrice = $grnItem["itemUnitPrice"] + ($oneItemAllocatedPrice / $oneItemReceivedQty);
                        // $oneItemgoodsGroup = $grnItem["itemInvoiceGoodsGroup"] ?? "";

                        $item_query = queryGet('SELECT * FROM erp_inventory_items WHERE itemId="' . $oneItemId . '"', false);
                        $oneItemgoodsGroup = $item_query["data"]["goodsGroup"];

                        $logRef = $grnCode;

                        if ($oneItemgoodsType == "goods") {

                            if (isset($grnItem["activateBatch"]) && $grnItem["activateBatch"] == 1) {
                                foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                    $batchNumber = $multipleBatch["batchNumber"];
                                    $batchQuantity = $multipleBatch["quantity"];
                                    $bin = $multipleBatch["bin"];
                                    $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                    $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                    $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                    $rack_id = $layer_query["data"]["rack_id"] ?? 0;


                                    if (is_null($batchNumber) || $batchNumber == "") {
                                        $batchNumber = $logRef;
                                    }

                                    $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                    $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                    $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                    $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`parentId`='.$grnId.',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $batchNumber . '", `refNumber`="' . $grnNo . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                    $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                    $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $batchNumber . '", `qty` = "' . $batchQuantity . '", `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`= "' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                    $binInsertObj = queryInsert($binMappingInsert);
                                    // return $stockLogInsertObj;

                                    if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                        $errorsItemStockLogInsert++;
                                    }
                                }
                            } else {
                                foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                    // $batchNumber = $multipleBatch["batchNumber"];
                                    $batchQuantity = $multipleBatch["quantity"];
                                    $bin = $multipleBatch["bin"];
                                    $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                    $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                    $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                    $rack_id = $layer_query["data"]["rack_id"] ?? 0;

                                    $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                    $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                    $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                    $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`parentId`='.$grnId.',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $grnNo . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                    $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                    $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $logRef . '", `qty` = ' . $batchQuantity . ', `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`="' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                    $binInsertObj = queryInsert($binMappingInsert);
                                    // return $stockLogInsertObj;
                                    // console($binInsertObj);

                                    if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                        $errorsItemStockLogInsert++;
                                    }
                                }
                            }
                        }
                    }
                }

                //Acc Bacic Details Start-------------------------------
                $grnPostingAccountingData = [
                    "documentNo" => $documentNo,
                    "documentDate" => $documentDate,
                    "invoiceDueDate" => $invoiceDueDate,
                    "invoicePostingDate" => $invoicePostingDate,
                    "referenceNo" => $grnCode,
                    "journalEntryReference" => 'Purchase',
                    "remarks" => addslashes($remarks),
                    "grnItemList" => $grnItemListAcc,
                    "party_code" => $vendor_code,
                    "party_name" =>  $vendor_name,
                    "type" => $grnType
                ];
                //End---------------------------------------------------

                // echo "***************------*****************";
                // echo $errorsInGrnItemsAdd;
                // echo "********************************";
                // echo $errorsItemStockSummaryUpdate;
                // echo "********************************";
                // echo $errorGrnSubmit;
                // echo "***************------*****************";
                // console($grnPostingAccountingData);

                if ($errorsInGrnItemsAdd == 0  &&  $errorGrnSubmit == 0) {
                    if ($grnType == 'grn') {
                        $grnAccPostingObj = $this->grnAccountingPosting($grnPostingAccountingData, $grnType, $grnId);     
                    } else {
                        $grnAccPostingObj = $this->srnAccountingPosting($grnPostingAccountingData, $grnType, $grnId);
                    }
                    if ($grnAccPostingObj["status"] == "success" && $grnAccPostingObj["journalId"] != "") {
                        $queryObj = queryUpdate('UPDATE `' . ERP_GRN . '` SET `grnPostingJournalId`=' . $grnAccPostingObj["journalId"] . ' WHERE `grnId`=' . $grnId);
                        if($queryObj['status']=="success")
                        {
                            if ($errorsInGrnItemsAdd == 0) {
                                foreach ($allItems as $grnItem) {
                                    $oneItemId = $grnItem["itemId"];
                                    $oneItemCode = $grnItem["itemCode"];
                                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                                    if ($oneItemgoodsType == "goods") {
                                        $oneItemStocksQty = $grnItem["itemReceivedQty"] ?? 0.00; //500
                                        $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50
                                        $oneItemAllocatedPrice = $grnItem["allocatedCost"] ?? 0.00; //50
                                        $oneItemUnitPrice = $grnItem["itemUnitPrice"] + ($oneItemAllocatedPrice / $oneItemStocksQty);
                                        $mwp = calculateNewMwp($oneItemId, $oneItemStocksQty, $oneItemUnitPrice, "GRN");
                                    }
                                }
                            }
                        }
                    } else {
                        $accerror++;
                        $logAccFailedResponce = $this->failedAccController->logAccountingFailure($grnId, $grnType);
                    }

                    $auditTrailreturn = generateAuditTrail($auditTrail);
                }
            }
        }

        // return $errorArray;

        


        // exit();

        if ($accerror == 0) {

            // if ($grnType == 'grn') {

            return [
                "status" => "success",
                "message" => strtoupper($grnType) . " posted successfully, " . $grnCode,
                "acc" => $grnAccPostingObj,
                "goodStockInsertObj" => $goodStockInsertObj,
                "goodStockInserSql" => $goodStockInserSql,
                "grnPostingAccountingData" => $grnPostingAccountingData
            ];
            // } else {
            //     return [
            //         "status" => "success",
            //         "message" => ($grnApprovedStatus == "pending") ? "SRN posted successfully, waiting for 'approval'" : "SRN posted successfully." . $grnCode,
            //         // "acc" => $grnAccPostingObj,
            //         "goodStockInsertObj" => $goodStockInsertObj,
            //         "goodStockInserSql" => $goodStockInserSql,
            //         "grnPostingAccountingData" => $grnPostingAccountingData
            //     ];
            // }
        } else {
            if($errorGrnSubmit==0 && $errorsInGrnItemsAdd==0 && $errorsItemStockSummaryUpdate==0 && $errorsItemStockLogInsert==0)
            {

                return [
                    "status" => "warning",
                    "sql" => $oneItemInsertQuery,
                    "message" => strtoupper($grnType) . " posted successfully, but account entry failed. Please check.",
                    "grnPostingAccountingData" => ''
                ];
            } else {


                return [
                    "status" => "warning",
                    "sql" => $oneItemInsertQuery,
                    "message" => "GRN posted failed, try again! " . $errorGrnSubmit . "," . $errorsInGrnItemsAdd . "," . $errorsItemStockSummaryUpdate . "," . $errorsItemStockLogInsert,
                    "grnPostingAccountingData" => ''
                ];
            }
        }
    }

    function calculateTotalTax($items)
    {
        $taxSummary = [];

        foreach ($items as $item) {
            if (isset($item['hiddenTaxValues'])) {
                $taxValues = json_decode($item['hiddenTaxValues'], true);

                if (is_array($taxValues)) {
                    foreach ($taxValues as $tax) {
                        $key = $tax['gstType'];

                        if (!isset($taxSummary[$key])) {
                            $taxSummary[$key] = [
                                "gstType" => $tax['gstType'],
                                "taxPercentage" => $tax['taxPercentage'],
                                "taxAmount" => 0
                            ];
                        }

                        $taxSummary[$key]['taxAmount'] += (float)($tax['taxAmount'] ?? 0);
                    }
                }
            }
        }

        // Format the tax amounts
        foreach ($taxSummary as &$summary) {
            $summary['taxAmount'] = number_format($summary['taxAmount'], 2, '.', '');
        }

        // Return the JSON formatted string for direct DB insert (without pretty print)
        return json_encode(array_values($taxSummary));
    }

    function createManualGrn2($INPUTS)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $isQaEnabled;
        $returnData = [];
        global $companyCountry;


        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "grnItemList" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputs";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        $po_date = $INPUTS["po_date"] ?? "";
        $po_date = $INPUTS["invoicePostingDate"] ?? "";

        // ["PO12345","PO12345"]
        $grnPoNumberString = json_decode($INPUTS["invoicePoNumber"], true);
        $grnPoNumber = implode('|', $grnPoNumberString);

        $invoicePostingDate = $INPUTS["invoicePostingDate"];

        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";

        $locationGstinStateName = $INPUTS["locationGstinStateName"];


        $grnItemList = $INPUTS["grnItemList"];
        $multiple_id = $INPUTS["id"];
        $currency = $INPUTS['currency'];
        $rate = $INPUTS['currency_conversion_rate'];
        if (isset($INPUTS['funcArea'])) {
            $func = $INPUTS['funcArea'];
        } else {
            $func = 0;
        }

        $extra_remark = $INPUTS['extra_remark'] ?? '';


        $grnApprovedStatus = "approved";

        $errorsItemStockLogInsert = 0;
        $errorsInGrnItemsAdd = 0;
        $errorsItemStockSummaryUpdate = 0;
        $errorGrnSubmit = 0;
        $accerror = 0;
        $allSubTotal = 0;
        $errorArray = [];
        foreach ($grnItemList as $vendor_id => $allItems) {

            $documentNo = $INPUTS["documentNo"][$vendor_id];
            $documentDate = $INPUTS["documentDate"][$vendor_id];
            $invoiceDueDate = $INPUTS["invoiceDueDate"][$vendor_id];

            if (isset($_FILES["invoice_file_name"]) && $_FILES["invoice_file_name"] != "") {
                $file_tmpname = $_FILES["invoice_file_name"]["tmp_name"][$vendor_id];
                $file_name = $_FILES["invoice_file_name"]['name'][$vendor_id];
                $file_size = $_FILES["invoice_file_name"]['size'][$vendor_id];

                $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
                $maxsize = 2 * 1024 * 1024; // 10 MB

                $uploadedInvoiceObj = uploadFile(["name" => $file_name, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);

                if ($uploadedInvoiceObj["status"] == "success") {
                    $uploadedInvoiceName = $uploadedInvoiceObj["data"];
                } else {
                    $uploadedInvoiceName = "";
                }
            } else {
                $uploadedInvoiceName = null;
            }

            $vendorDocumentFile = $uploadedInvoiceName;

            if ($this->checkAllGoods($allItems)) {
                echo "SRN";
                $grnNo = "SRN" . time() . rand(100, 999);
                $grnType = "srn";
                $remarks = "SRN By PO " . $grnNo . " " . $extra_remark;
            } else {
                echo "GRN";
                $grnNo = "GRN" . time() . rand(100, 999);
                $grnType = "grn";
                $remarks = "GRN By PO " . $grnNo . " " . $extra_remark;
            }

            $grnCode = $grnNo;

            $vendor_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id`='" . $company_id . "' AND `vendor_id` = '" . $vendor_id . "'", false);
            $vendor_code = $vendor_details["data"]["vendor_code"];
            $vendor_gst = $vendor_details["data"]["vendor_gstin"];
            $vendor_name = $vendor_details["data"]["trade_name"];

            $sumTaxAmount = 0;
            $allItemUnitPrice = array_sum(array_column($allItems, 'itemUnitPrice'));
            $allItemQty = array_sum(array_column($allItems, 'itemReceivedQty'));
            if ($companyCountry == 103) {
                $allSubTotal = array_sum(array_column($allItems, 'itemTotalPrice'));
                $totalInvoiceCGST = array_sum(array_column($allItems, 'itemCGST'));
                $totalInvoiceSGST = array_sum(array_column($allItems, 'itemSGST'));
                $totalInvoiceIGST = array_sum(array_column($allItems, 'itemIGST'));
                $totalTds = array_sum(array_column($allItems, 'itemInvoiceTDSValue'));
                $taxComponents = json_encode($this->calculateTotalTax($allItems));
            } else {
                $allSubTotal = array_sum(array_column($allItems, 'itemTotalPrice'));
                $totalTds = array_sum(array_column($allItems, 'itemInvoiceTDSValue'));



                $taxComponents = json_encode($this->calculateTotalTax($allItems));
                $datasum = json_decode($taxComponents, true);


                $array = json_decode($datasum, true);

                $taxTrailArray = [];

                if (!empty($array)) {
                    foreach ($array as $tax) {
                        $taxTrailArray[$tax['gstType']] = decimalValuePreview($tax['taxAmount']);
                    }
                    $gstTrailFlag = 1;
                } else {
                    $gstTrailFlag = 0;
                }
                // Sum taxAmount
                $sumTaxAmount = array_sum(array_column($array, 'taxAmount'));


                $totalInvoiceCGST = 0;
                $totalInvoiceSGST = 0;
                $totalInvoiceIGST = 0;
            }

            $totalInvoiceTotal = $allSubTotal + $totalInvoiceCGST + $totalInvoiceSGST + $totalInvoiceIGST - $totalTds + $sumTaxAmount;

            if ($vendor_gst == "" || $vendor_gst == NULL || !isset($vendor_gst)) {
                $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
            } else {
                $vendorGstinStateCode = substr($vendor_gst, 0, 2);
            }


            $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";

            if ($companyCountry != 103) {
                $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendor_id AND `vendor_business_primary_flag` = 1");
                $b_row = $b_places['data'];
                $vendorGstinStateName = $b_row['vendor_business_state'];
                $venderabn = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendor_id");

                $abn = $venderabn['data'];
                $vendor_gst = $abn['vendor_gstin'];
            }
            $insertgrnSql = 'INSERT INTO `' . ERP_GRN . '` SET 
                            `companyId`="' . $company_id . '",
                            `branchId`="' . $branch_id . '",
                            `locationId`="' . $location_id . '",
                            `pending_grn_id`="' . $multiple_id . '",
                            `functionalAreaId`="",
                            `grnCode`="' . $grnNo . '",
                            `grnType`="' . $grnType . '",
                            `grnPoNumber`="' . $grnPoNumber . '",
                            `po_date`="' . $po_date . '",
                            `vendorId`=' . $vendor_id . ',
                            `vendorCode`="' . $vendor_code . '",
                            `vendorGstin`="' . $vendor_gst . '",
                            `vendorName`="' . $vendor_name . '",
                            `vendorDocumentNo`="' . $documentNo . '",
                            `vendorDocumentDate`="' . $documentDate . '",
                            `postingDate`="' . $invoicePostingDate . '",
                            `dueDate`="' . $invoiceDueDate . '",
                            `dueDays`="' . $invoiceDueDays . '",
                            `paymentStatus`="1",
                            `dueAmt`="' . $totalInvoiceTotal . '",
                            `grnSubTotal`="' . $allSubTotal . '",
                            `grnTotalCgst`="' . $totalInvoiceCGST . '",
                            `grnTotalSgst`="' . $totalInvoiceSGST . '",
                            `grnTotalIgst`="' . $totalInvoiceIGST . '",
                            `grnTotalTds`="' . $totalTds . '",
                            `taxComponents`=' . $taxComponents . ',
                            `grnTotalAmount`="' . $totalInvoiceTotal . '",
                            `locationGstinStateName`="' . $locationGstinStateName . '",
                            `vendorGstinStateName`="' . $vendorGstinStateName . '",
                            `vendorDocumentFile`="' . $vendorDocumentFile . '",
                            `grnCreatedBy`="' . $created_by . '",
                            `grnUpdatedBy`="' . $updated_by . '",
                            `currency` = "' . $currency . '",
                            `conversion_rate` = "' . $rate . '",
                            `functional_area` = "' . $func . '" ,
                            `grnApprovedStatus`="' . $grnApprovedStatus . '"';


            $inserGrnObj = queryInsert($insertgrnSql);



            if ($inserGrnObj["status"] != "success") {
                $errorGrnSubmit++;
            } else {

                $grnId = $inserGrnObj["insertedId"];

                //Enter PO ID in relation table

                $splitted_po = explode("|", $grnPoNumber);

                foreach ($splitted_po as $each_po) {

                    $check_po = substr($each_po, 0, 2);

                    if ($check_po == "PO") {

                        $poId = queryGet("SELECT `po_id` FROM `erp_branch_purchase_order` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `po_number`='" . $each_po . "'", false)["data"]["po_id"] ?? 0;

                        $relationSql = 'INSERT INTO `erp_grn_po_relation` SET 
                                `grn_id`="' . $grnId . '",
                                `po_id`="' . $poId . '",
                                `po_number`="' . $each_po . '",
                                `grn_number`="' . $grnNo . '"';

                        $relationObj = queryInsert($relationSql);
                    }
                }


                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_GRN;
                $auditTrail['basicDetail']['column_name'] = 'grnId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $grnId;  // primary key
                $auditTrail['basicDetail']['document_number'] = $grnCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_type'] = 'vendor';
                $auditTrail['basicDetail']['party_id'] = $vendor_id;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = $grnType . ' Creation';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insertgrnSql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Basic Details']['GRN_Code'] = $grnCode;
                $auditTrail['action_data']['Basic Details']['GRN_Type'] = $grnType;
                $auditTrail['action_data']['Basic Details']['GRN PO Number'] = $grnPoNumber;
                $auditTrail['action_data']['Basic Details']['PO_Date'] = formatDateWeb($po_date);
                $auditTrail['action_data']['Basic Details']['Vendor_Code'] = $vendor_code;
                $auditTrail['action_data']['Basic Details']['Vendor_Gstin'] = $vendor_gst;
                $auditTrail['action_data']['Basic Details']['Vendor_Name'] = $vendor_name;
                $auditTrail['action_data']['Basic Details']['Vendor Document No'] = $documentNo;
                $auditTrail['action_data']['Basic Details']['Vendor Document Date'] = formatDateORDateTime($documentDate);
                $auditTrail['action_data']['Basic Details']['Posting_Date'] = formatDateORDateTime($invoicePostingDate);
                $auditTrail['action_data']['Basic Details']['Due_Date'] = formatDateORDateTime($invoiceDueDate);
                $auditTrail['action_data']['Basic Details']['Due_Days'] = $invoiceDueDays;
                $auditTrail['action_data']['Basic Details']['Payment_Status'] = "1";
                $auditTrail['action_data']['Basic Details']['Due_Amt'] = decimalValuePreview($totalInvoiceTotal);
                $auditTrail['action_data']['Basic Details']['GRN Sub Total'] = decimalValuePreview($allSubTotal);

                if ($gstTrailFlag == 1) {
                    foreach ($taxTrailArray as $taxType => $amount) {
                        $auditTrail['action_data']['Basic Details']["GRN Total $taxType"] = decimalValuePreview($amount);
                    }
                } else {
                    $auditTrail['action_data']['Basic Details']['GRN Total Cgst'] = decimalValuePreview($totalInvoiceCGST);
                    $auditTrail['action_data']['Basic Details']['GRN Total Sgst'] = decimalValuePreview($totalInvoiceSGST);
                    $auditTrail['action_data']['Basic Details']['GRN Total Igst'] = decimalValuePreview($totalInvoiceIGST);
                }

                $auditTrail['action_data']['Basic Details']['GRN Total TDS'] = decimalValuePreview($totalTds);
                $auditTrail['action_data']['Basic Details']['GRN Total Amount'] = decimalValuePreview($totalInvoiceTotal);
                $auditTrail['action_data']['Basic Details']['Location Gstin State Name'] = $locationGstinStateName;
                $auditTrail['action_data']['Basic Details']['Vendor Gstin State Name'] = $vendorGstinStateName;
                $auditTrail['action_data']['Basic Details']['Vendor Document File'] = $vendorDocumentFile;
                $auditTrail['action_data']['Basic Details']['GRN Created By'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Basic Details']['GRN Updated By'] = getCreatedByUser($updated_by);
                $auditTrail['action_data']['Basic Details']['Currency'] = getSingleCurrencyType($currency);
                $auditTrail['action_data']['Basic Details']['Conversion_Rate'] = $rate;
                $auditTrail['action_data']['Basic Details']['Functional_Area'] = $func;
                $auditTrail['action_data']['Basic Details']['GRN Approved Status'] = $grnApprovedStatus;

                $grnItemListAcc = [];
                $postatusFlug = 0;
                foreach ($allItems as $oneItemLineId => $grnItem) {
                    $oneItemId = $grnItem["itemId"] ?? 0;
                    $oneItemCode = $grnItem["itemCode"];
                    $oneItemHsn = $grnItem["itemHsn"];
                    $oneItemName = addslashes($grnItem["itemName"]);
                    $oneItemQty = $grnItem["itemQty"];
                    $oneItemReceiveQty = $grnItem["itemReceivedQty"];
                    $oneItemTax = $grnItem["itemTax"];
                    $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                    $oneItemSubTotalPrice = $oneItemUnitPrice * $oneItemReceiveQty;
                    $oneItemTotalPrice = $grnItem["itemGrandTotalPrice"];
                    if ($grnItem["itemStorageLocationId"] == "inventorise_" . $oneItemLineId) {
                        $oneItemStorageLocationId = 0;
                    } else {
                        $oneItemStorageLocationId = $grnItem["itemStorageLocationId"] ?? 0;
                    }

                    $oneItemStocksQty = $grnItem["itemStockQty"] ?? 0;
                    $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                    $oneItemUOM = $grnItem["itemUOM"] ?? "";
                    ///////////////////////////////////////////////////////////
                    $oneItemCGST = $grnItem["itemCGST"] ?? "0.0";
                    $oneItemSGST = $grnItem["itemSGST"] ?? "0.0";
                    $oneItemIGST = $grnItem["itemIGST"] ?? "0.0";
                    $oneItemTDS = $grnItem["itemTds"] ?? "0.0";
                    $taxComponentsitem = $grnItem["hiddenTaxValues"] ?? "";
                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";
                    $oneItemRemainQty = $grnItem["itemRemainQty"] ?? 0;
                    $onePoItemId = $grnItem["PoItemId"] ?? 0;
                    $PoNumber = $grnItem["itemPurchaseOrder"] ?? 0;
                    $allocated_array = $grnItem["allocated_array"] ?? "";

                    $oneItemInsertQuery = "INSERT INTO `" . ERP_GRN_GOODS . "` 
                                            SET `grnId`='" . $grnId . "',
                                                `grnCode`='" . $grnCode . "',
                                                `goodName`='" . $oneItemName . "',
                                                `goodDesc`='',
                                                `goodId`='" . $oneItemId . "',
                                                `goodCode`='" . $oneItemCode . "',
                                                `goodHsn`='" . $oneItemHsn . "',
                                                `goodQty`='" . $oneItemReceivedQty . "',
                                                `receivedQty`='" . $oneItemReceivedQty . "',
                                                `unitPrice`='" . $oneItemUnitPrice . "',
                                                `cgst`='" . $oneItemCGST . "', 
                                                `sgst`='" . $oneItemSGST . "',
                                                `igst`='" . $oneItemIGST . "',
                                                `tds`='" . $oneItemTDS . "',
                                                `taxComponents`='" . $taxComponentsitem . "',
                                                `goodstype`='" . $oneItemgoodsType . "',
                                                `totalAmount`='" . $oneItemTotalPrice . "',
                                                `itemStocksQty`='" . $oneItemReceivedQty . "',
                                                `itemUOM`='" . $oneItemUOM . "', 
                                                `itemStorageLocation`=" . $oneItemStorageLocationId . ",
                                                `grnType`='" . $grnType . "', 
                                                `allocated_array`='" . $allocated_array . "',
                                                `grnGoodCreatedBy`='" . $created_by . "',
                                                `grnGoodUpdatedBy`='" . $updated_by . "'";

                    $oneItemInsertObj = queryInsert($oneItemInsertQuery);

                    array_push($errorArray, $oneItemInsertObj);

                    if ($oneItemInsertObj["status"] != "success") {
                        $errorsInGrnItemsAdd++;
                    } else {

                        $lastgrnitem_id = $oneItemInsertObj['insertedId'];
                        // if ($oneItemgoodsType == "service") {
                        //     foreach ($grnItem['cost_center'] as $costcenter) {
                        //         $costcenter = $costcenter['name'];
                        //         $costcenter_rate = $costcenter['rate'];
                        //         $costcenter_id = $costcenter['id'];
                        //         $cost_center = queryInsert("INSERT INTO `erp_srn_costcenter_details` SET `srn_id` = $grnId, `costcenter_id`= $costcenter_id, `costcenter_percentage`=$costcenter_rate ,`srn_item_id`=$lastgrnitem_id");
                        //     }
                        // }
                        queryUpdate('UPDATE `erp_branch_purchase_order_items` SET `remainingQty`=' . $oneItemRemainQty . ' WHERE `po_item_id`="' . $onePoItemId . '"');

                        $po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` WHERE remainingQty > 0 AND po_item_id = '" . $onePoItemId . "'", true);

                        if ($po_item["numRows"] > 0) {
                            $postatusFlug++;
                        }

                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN_Code'] = $grnCode;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Name'] = $oneItemName;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Desc'] = "";
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Code'] = $oneItemCode;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_HSN'] = $oneItemHsn;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Good_Qty'] = decimalQuantityPreview($oneItemQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Received_Qty'] = decimalQuantityPreview($oneItemReceivedQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Unit_Price'] = decimalValuePreview($oneItemUnitPrice);

                        if ($companyCountry == 103) {
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['CGST'] = decimalValuePreview($oneItemCGST);
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['SGST'] = decimalValuePreview($oneItemSGST);
                            $auditTrail['action_data']['Goods Details'][$oneItemCode]['IGST'] = decimalValuePreview($oneItemIGST);
                        } else {
                            $taxComponentsItem = $grnItem["hiddenTaxValues"] ?? "";
                            $taxArrayItem = json_decode($taxComponentsItem, true);
                        
                            if (!empty($taxArrayItem)) {
                                foreach ($taxArrayItem as $itemTax) {
                                    $taxType = $itemTax['gstType'] ?? '';
                                    $taxAmt = decimalValuePreview($itemTax['taxAmount'] ?? 0);
                                    if ($taxType != '') {
                                        $auditTrail['action_data']['Goods Details'][$oneItemCode]["$taxType"] = $taxAmt;
                                    }
                                }
                            }
                        }

                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['TDS'] = decimalValuePreview($oneItemTDS);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Total_Amount'] = decimalValuePreview($oneItemTotalPrice);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Item Stocks Qty'] = decimalQuantityPreview($oneItemStocksQty);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['Item_UOM'] = $oneItemUOM;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN_Type'] = $grnType;
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN Good Created By'] = getCreatedByUser($created_by);
                        $auditTrail['action_data']['Goods Details'][$oneItemCode]['GRN Good Updated By'] = getCreatedByUser($updated_by);
                    }


                    $allocatedArray = json_decode($grnItem['allocated_array'], true);

                    if (isset($allocatedArray) && count($allocatedArray) > 0) {
                        foreach ($allocatedArray as $key => $value) {
                            $from_item_id = $value["formItemId"];
                            $to_item_id = $value["toItemId"];
                            $allocated_cost = $value["allocatedCost"];
                            $to_vendor_id = $value["toVendorId"];
                            $alloItem_item = queryGet("SELECT parentGlId,itemCode,itemName FROM `erp_inventory_items` WHERE itemId = $to_item_id")['data'];
                            $alloItemName = $alloItem_item['itemName'];
                            $alloItemCode = $alloItem_item['itemCode'];
                            $alloItemGl = $alloItem_item['parentGlId'];


                            $allocInsertSql = 'INSERT INTO `erp_cost_allocation` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`grn_code`="' . $grnCode . '",`grn_id`=' . $grnId . ', `from_item_id`=' . $from_item_id . ',`to_item_id`=' . $to_item_id . ',`to_vendor_id`=' . $to_vendor_id . ',`allocated_cost`=' . $allocated_cost . ', `created_by`="' . $created_by . '"';

                            $allocInsertSqlObj = queryInsert($allocInsertSql);

                            if ($allocInsertSqlObj["status"] != "success") {
                                $errorsItemStockLogInsert++;
                            }

                            $grnItemListAcc[$oneItemLineId . $to_item_id]["parentGlId"] = $alloItemGl;
                            $grnItemListAcc[$oneItemLineId . $to_item_id]["itemCode"] = $alloItemCode;
                            $grnItemListAcc[$oneItemLineId . $to_item_id]["itemName"] = $alloItemName;
                            $grnItemListAcc[$oneItemLineId . $to_item_id]["itemTotalPrice"] = $allocated_cost;
                            $grnItemListAcc[$oneItemLineId . $to_item_id]["remark"] = $alloItemName . ' [' . $alloItemCode . "] Allocate Cost from" . $oneItemName . ' [' . $oneItemCode . ']';
                            $grnItemListAcc[$oneItemLineId . $to_item_id]["type"] = 'service';
                            // console($grnItemListAcc);

                        }
                    } else {

                        $grnItemListAcc[$oneItemLineId]["parentGlId"] = $this->getInventoryItemParentGl($grnItem["itemId"]);
                        $grnItemListAcc[$oneItemLineId]["itemCode"] = $oneItemCode;
                        $grnItemListAcc[$oneItemLineId]["itemName"] = $oneItemName;
                        $grnItemListAcc[$oneItemLineId]["itemTotalPrice"] = $oneItemSubTotalPrice;
                        $grnItemListAcc[$oneItemLineId]["remark"] = $oneItemCode;
                        $grnItemListAcc[$oneItemLineId]["type"] = $oneItemgoodsType;
                    }
                }
                if ($postatusFlug == 0) {
                    //Update to close
                    $close = 10;
                    queryUpdate('UPDATE `erp_branch_purchase_order` SET `po_status`=' . $close . ' WHERE location_id=' . $location_id . ' AND company_id=' . $company_id . ' AND branch_id=' . $branch_id . ' AND `po_number`="' . $PoNumber . '"');
                }

                // Row material stocks entry 
                // new update on 12-12-2022 18:35pm

                if ($errorsInGrnItemsAdd == 0) {
                    foreach ($allItems as $grnItem) {
                        $oneItemId = $grnItem["itemId"];
                        $oneItemCode = $grnItem["itemCode"];
                        $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                        if ($oneItemgoodsType == "goods") {
                            $oneItemStocksQty = $grnItem["itemReceivedQty"] ?? 0.00; //500
                            $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50
                            $oneItemAllocatedPrice = $grnItem["allocatedCost"] ?? 0.00; //50

                            $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId);

                            $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0; //3000.00
                            $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0; //50.00
                            $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                            $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                            $itemNewTotalPrice = (float)$prevTotalPrice + (($oneItemStocksQty * $oneItemUnitPrice) + $oneItemAllocatedPrice); //(150000 + (500 * 50)) //175000
                            $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                            if (is_nan($movingWeightedPrice)) {
                                $movingWeightedPrice = 0;
                            }

                            if ($goodStockSummaryCheckSql["numRows"] > 0) {

                                $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;

                                // $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                // if ($checkfgrm["data"]["storage_location_material_type"] == "RM" && $checkfgrm["data"]["storage_location_type"] == "RM-WH") {
                                //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `rmWhOpen`=`rmWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                                // } elseif ($checkfgrm["data"]["storage_location_material_type"] == "FG" && $checkfgrm["data"]["storage_location_type"] == "FG-WH") {
                                //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `fgWhOpen`=`fgWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                                // }

                                $goodStockInsertObj = queryUpdate($goodStockInserSql);
                                if ($goodStockInsertObj["status"] != "success") {
                                    $errorsItemStockSummaryUpdate++;
                                    // return $goodStockInserSql;
                                    //  $goodStockInsertObj;
                                }

                                queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $movingWeightedPrice . ',`createdBy`="' . $created_by . '"');
                            } else {

                                $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;

                                $goodStockInsertObj = queryInsert($goodStockInserSql);

                                if ($goodStockInsertObj["status"] != "success") {
                                    $errorsItemStockSummaryUpdate++;
                                } else {
                                    ///---------------------------------Audit Log Start---------------------
                                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                                    $auditTrailSummry = array();
                                    $auditTrailSummry['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                                    $auditTrailSummry['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                                    $auditTrailSummry['basicDetail']['column_name'] = 'itemId'; // Primary key column
                                    $auditTrailSummry['basicDetail']['document_id'] = $oneItemId;  //     primary key
                                    $auditTrailSummry['basicDetail']['document_number'] = $oneItemCode;
                                    $auditTrailSummry['basicDetail']['action_code'] = $action_code;
                                    $auditTrailSummry['basicDetail']['action_referance'] = $grnCode;
                                    $auditTrailSummry['basicDetail']['action_title'] = 'Item Stock added';  //Action comment
                                    $auditTrailSummry['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                                    $auditTrailSummry['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                                    $auditTrailSummry['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                                    $auditTrailSummry['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                                    $auditTrailSummry['basicDetail']['action_sqlQuery'] = base64_encode($goodStockInserSql);
                                    $auditTrailSummry['basicDetail']['others'] = '';
                                    $auditTrailSummry['basicDetail']['remark'] = '';



                                    $auditTrailSummry['action_data']['Summary']['Item Total Qty'] = decimalQuantityPreview($itemNewTotalQty);
                                    $auditTrailSummry['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($movingWeightedPrice);
                                    $auditTrailSummry['action_data']['Summary']['RMWH_Open'] = decimalQuantityPreview($oneItemStocksQty);

                                    $auditTrailreturn = generateAuditTrail($auditTrailSummry);
                                }

                                queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $oneItemUnitPrice . ',`createdBy`="' . $created_by . '"');
                            }
                        }
                    }
                }

                // Row material stocks entry log entry

                if ($errorsItemStockSummaryUpdate == 0) {

                    foreach ($allItems as $grnItem) {
                        $oneItemId = $grnItem["itemId"];
                        $oneItemCode = $grnItem["itemCode"];
                        $oneItemHsn = $grnItem["itemHsn"];
                        $oneItemName = addslashes($grnItem["itemName"]);
                        $oneItemQty = $grnItem["itemQty"];
                        $oneItemUomId = $grnItem["itemUOMID"] ?? "";
                        $oneItemTax = $grnItem["itemTax"];
                        $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                        $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                        $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                        $oneItemStocksQty = $grnItem["itemStockQty"] ?? $oneItemQty;
                        $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                        $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";
                        // $oneItemgoodsGroup = $grnItem["itemInvoiceGoodsGroup"] ?? "";

                        $item_query = queryGet('SELECT * FROM erp_inventory_items WHERE itemId="' . $oneItemId . '"', false);
                        $oneItemgoodsGroup = $item_query["data"]["goodsGroup"];

                        $logRef = $grnCode;

                        if ($oneItemgoodsType == "goods") {

                            if (isset($grnItem["activateBatch"]) && $grnItem["activateBatch"] == 1) {
                                foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                    $batchNumber = $multipleBatch["batchNumber"];
                                    $batchQuantity = $multipleBatch["quantity"];
                                    $bin = $multipleBatch["bin"];
                                    $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                    $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                    $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                    $rack_id = $layer_query["data"]["rack_id"] ?? 0;


                                    if (is_null($batchNumber) || $batchNumber == "") {
                                        $batchNumber = $logRef;
                                    }

                                    $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                    $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                    $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                    $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`parentId`='.$grnId.',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $batchNumber . '", `refNumber`="' . $grnNo . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                    $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                    $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $batchNumber . '", `qty` = "' . $batchQuantity . '", `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`= "' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                    $binInsertObj = queryInsert($binMappingInsert);
                                    // return $stockLogInsertObj;

                                    if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                        $errorsItemStockLogInsert++;
                                    }
                                }
                            } else {
                                foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                    // $batchNumber = $multipleBatch["batchNumber"];
                                    $batchQuantity = $multipleBatch["quantity"];
                                    $bin = $multipleBatch["bin"];
                                    $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                    $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                    $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                    $rack_id = $layer_query["data"]["rack_id"] ?? 0;

                                    $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                    $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                    $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                    $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`parentId`='.$grnId.',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $grnNo . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                    $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                    $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $logRef . '", `qty` = ' . $batchQuantity . ', `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`="' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                    $binInsertObj = queryInsert($binMappingInsert);
                                    // return $stockLogInsertObj;
                                    // console($binInsertObj);

                                    if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                        $errorsItemStockLogInsert++;
                                    }
                                }
                            }
                        }
                    }
                }

                //Acc Bacic Details Start-------------------------------
                $grnPostingAccountingData = [
                    "documentNo" => $documentNo,
                    "documentDate" => $documentDate,
                    "invoiceDueDate" => $invoiceDueDate,
                    "invoicePostingDate" => $invoicePostingDate,
                    "referenceNo" => $grnCode,
                    "journalEntryReference" => 'Purchase',
                    "remarks" => addslashes($remarks),
                    "grnItemList" => $grnItemListAcc,
                    "party_code" => $vendor_code,
                    "party_name" =>  $vendor_name,
                    "type" => $grnType
                ];
                //End---------------------------------------------------

                // echo "***************------*****************";
                // echo $errorsInGrnItemsAdd;
                // echo "********************************";
                // echo $errorsItemStockSummaryUpdate;
                // echo "********************************";
                // echo $errorGrnSubmit;
                // echo "***************------*****************";
                // console($grnPostingAccountingData);

                if ($errorsInGrnItemsAdd == 0 && $errorsItemStockSummaryUpdate == 0 &&  $errorGrnSubmit == 0) {
                    if ($grnType == 'grn') {
                        $grnAccPostingObj = $this->grnAccountingPosting($grnPostingAccountingData, $grnType, $grnId);
                    } else {
                        $grnAccPostingObj = $this->srnAccountingPosting($grnPostingAccountingData, $grnType, $grnId);
                    }

                    if ($grnAccPostingObj["status"] == "success" && $grnAccPostingObj["journalId"] != "") {
                        $queryObj = queryUpdate('UPDATE `' . ERP_GRN . '` SET `grnPostingJournalId`=' . $grnAccPostingObj["journalId"] . ' WHERE `grnId`=' . $grnId);
                        
                    } else {
                        $accerror++;
                    }

                    $auditTrailreturn = generateAuditTrail($auditTrail);
                }
            }
        }

        // return $errorArray;

        // Posted GRN ITEMS allocated Price basis MWP change
        if (isset($INPUTS["grnItemPostedList"]) && count($INPUTS["grnItemPostedList"]) > 0) {
            $grnItemPostedList = $INPUTS["grnItemPostedList"];
            foreach ($grnItemPostedList as $grnItem) {
                $oneItemId = $grnItem["itemId"];
                $oneItemCode = $grnItem["itemCode"];
                $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                if ($oneItemgoodsType == "goods") {
                    $oneItemStocksQty = 1; //500
                    $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50
                    $oneItemAllocatedPrice = $grnItem["allocatedCost"] ?? 0.00; //50

                    $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId);

                    $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0; //3000.00
                    $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0; //50.00
                    $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                    $itemNewTotalQty = (float)$prevTotalQty; //3500
                    $itemNewTotalPrice = (float)$prevTotalPrice + ($oneItemStocksQty * $oneItemAllocatedPrice); //(150000 + (500 * 50)) //175000
                    $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                    if (is_nan($movingWeightedPrice)) {
                        $movingWeightedPrice = 0;
                    }

                    if ($goodStockSummaryCheckSql["numRows"] > 0) {

                        $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;


                        $goodStockInsertObj = queryUpdate($goodStockInserSql);
                        if ($goodStockInsertObj["status"] != "success") {
                            $errorsItemStockSummaryUpdate++;
                            // return $goodStockInserSql;
                            //  $goodStockInsertObj;
                        }

                        queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $movingWeightedPrice . ',`createdBy`="' . $created_by . '"');
                    } else {

                        $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;

                        $goodStockInsertObj = queryInsert($goodStockInserSql);

                        if ($goodStockInsertObj["status"] != "success") {
                            $errorsItemStockSummaryUpdate++;
                        } else {
                            ///---------------------------------Audit Log Start---------------------
                            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                            $auditTrailMWPChange = array();
                            $auditTrailMWPChange['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                            $auditTrailMWPChange['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                            $auditTrailMWPChange['basicDetail']['column_name'] = 'itemId'; // Primary key column
                            $auditTrailMWPChange['basicDetail']['document_id'] = $oneItemId;  //     primary key
                            $auditTrailMWPChange['basicDetail']['document_number'] = $oneItemCode;
                            $auditTrailMWPChange['basicDetail']['action_code'] = $action_code;
                            $auditTrailMWPChange['basicDetail']['action_referance'] = "Cost Allocation During SRN";
                            $auditTrailMWPChange['basicDetail']['action_title'] = 'Moving Weighted Price Change';  //Action comment
                            $auditTrailMWPChange['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                            $auditTrailMWPChange['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                            $auditTrailMWPChange['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                            $auditTrailMWPChange['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                            $auditTrailMWPChange['basicDetail']['action_sqlQuery'] = base64_encode($goodStockInserSql);
                            $auditTrailMWPChange['basicDetail']['others'] = '';
                            $auditTrailMWPChange['basicDetail']['remark'] = '';



                            $auditTrailMWPChange['action_data']['Summary']['Item Total Qty'] = decimalQuantityPreview($itemNewTotalQty);
                            $auditTrailMWPChange['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($movingWeightedPrice);
                            $auditTrailMWPChange['action_data']['Summary']['RMWH_Open'] = decimalValuePreview($oneItemStocksQty);

                            $auditTrailreturn = generateAuditTrail($auditTrailMWPChange);
                        }

                        queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $oneItemUnitPrice . ',`createdBy`="' . $created_by . '"');
                    }
                }
            }
        }

        // exit();

        if ($accerror == 0) {

            // if ($grnType == 'grn') {

            return [
                "status" => "success",
                "message" => strtoupper($grnType) . " posted successfully, " . $grnCode,
                "acc" => $grnAccPostingObj,
                "goodStockInsertObj" => $goodStockInsertObj,
                "goodStockInserSql" => $goodStockInserSql,
                "grnPostingAccountingData" => $grnPostingAccountingData
            ];
            // } else {
            //     return [
            //         "status" => "success",
            //         "message" => ($grnApprovedStatus == "pending") ? "SRN posted successfully, waiting for 'approval'" : "SRN posted successfully." . $grnCode,
            //         // "acc" => $grnAccPostingObj,
            //         "goodStockInsertObj" => $goodStockInsertObj,
            //         "goodStockInserSql" => $goodStockInserSql,
            //         "grnPostingAccountingData" => $grnPostingAccountingData
            //     ];
            // }
        } else {
            return [
                "status" => "warning",
                "sql" => $oneItemInsertQuery,
                "message" => "GRN posted failed, try again! " . $errorGrnSubmit . "," . $errorsInGrnItemsAdd . "," . $errorsItemStockSummaryUpdate . "," . $errorsItemStockLogInsert,
                "grnPostingAccountingData" => ''
            ];
        }
    }

    function createGrn($INPUTS)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $isQaEnabled;
        $returnData = [];
        //   $in = $INPUTS["grnItemList"];

        //   foreach($in as $data){
        //     foreach($data['cost_center'] as $costcenter){
        //         console($costcenter);


        //     }
        //   }

        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "grnItemList" => "array"
        ]);

        // "vendorGstin" => "required",
        // "totalInvoiceCGST" => "required",
        // "totalInvoiceSGST" => "required",
        // "totalInvoiceIGST" => "required",
        // "totalInvoiceSubTotal" => "required",
        // "totalInvoiceTotal" => "required",

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputs";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }



        if (isset($INPUTS["invoicePoNumber"]) && $INPUTS["invoicePoNumber"] != "") {
            $grnPoNumber = $INPUTS["invoicePoNumber"];
            $po_date = $INPUTS["po_date"];
            $check_po_exists = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_number`="' . $grnPoNumber . '" AND `status`= "active"');

            if ($check_po_exists["numRows"] == 0) {
                $returnData['status'] = "warning";
                $returnData['message'] = "Please Select proper Purchase Order Number";
                $returnData['errors'] = "";
                return $returnData;
            }
            $check_po_open = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_number`="' . $grnPoNumber . '" AND `po_status`= "9" AND `status`= "active"');

            if ($check_po_open["numRows"] == 0) {
                $returnData['status'] = "warning";
                $returnData['message'] = "Please Select Open Purchase Order Number";
                $returnData['errors'] = "";
                return $returnData;
            }

            $po_date = $check_po_open["data"]["po_date"];
        }

        if (isset($_FILES["invoice_file_name"]) && $_FILES["invoice_file_name"] != "") {
            $file_tmpname = $_FILES["invoice_file_name"]["tmp_name"];
            $file_name = $_FILES["invoice_file_name"]['name'];
            $file_size = $_FILES["invoice_file_name"]['size'];

            $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
            $maxsize = 2 * 1024 * 1024; // 10 MB

            $uploadedInvoiceObj = uploadFile(["name" => $file_name, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);

            if ($uploadedInvoiceObj["status"] == "success") {
                $uploadedInvoiceName = $uploadedInvoiceObj["data"];
            } else {
                $uploadedInvoiceName = "";
            }
        } else {
            $uploadedInvoiceName = $INPUTS["vendorDocumentFile"];
        }


        $grnCode = $INPUTS["grnCode"];
        $documentNo = $INPUTS["documentNo"];
        $documentDate = $INPUTS["documentDate"];
        $invoicePostingDate = $INPUTS["invoicePostingDate"];
        $invoiceDueDate = $INPUTS["invoiceDueDate"];
        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        $vendorId = $INPUTS["vendorId"];
        $vendorCode = $INPUTS["vendorCode"];
        $vendorName = addslashes($INPUTS["vendorName"]);
        $vendorGstin = $INPUTS["vendorGstin"] ?? '';
        $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
        $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
        $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
        $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
        $totalInvoiceTotal = $INPUTS["totalInvoiceTotal"];
        $locationGstinStateName = $INPUTS["locationGstinStateName"];
        $vendorGstinStateName = $INPUTS["vendorGstinStateName"] ?? '';
        $vendorDocumentFile = $uploadedInvoiceName;
        $totalTds = $INPUTS["totalInvoiceTDS"];
        $grnType = $INPUTS["grnType"];
        $grnItemList = $INPUTS["grnItemList"];
        $multiple_id = $INPUTS["id"];
        $currency = $INPUTS['currency'];
        $rate = $INPUTS['currency_conversion_rate'];
        $roundvalue = $INPUTS['roundvalue'] ?? 0.00;
        if (isset($INPUTS['funcArea'])) {
            $func = $INPUTS['funcArea'];
        } else {
            $func = 0;
        }

        $extra_remark = $INPUTS['extra_remark'] ?? '';


        $grnApprovedStatus = "approved";


        $check_invoice_exists = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `vendorDocumentNo` = '" . $documentNo . "' AND `vendorId`='" . $vendorId . "' AND `grnStatus`='active'");
        // $check_posted_grn = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $branchDeails["company_id"] . "' AND `vendorDocumentNo` = '" . $inv_no . "'");

        if ($check_invoice_exists["numRows"] != 0) {
            $returnData['status'] = "warning";
            $returnData['message'] = "This Invoice already exists";
            $returnData['errors'] = "";
            return $returnData;
        }



        foreach ($grnItemList as $itemKey => $grnItem) {
            if ($grnItem["itemQty"] != $grnItem["itemReceivedQty"]) {
                // $grnApprovedStatus = "pending";
                $grnApprovedStatus = "approved";
                // break;
            } else {
                $isCompanyPoEnabled = $this->getCompanyDetails()["isPoEnabled"] ?? "";
                if ($isCompanyPoEnabled) {
                    if ($grnPoNumber == "") {
                        // $grnApprovedStatus = "pending";
                        $grnApprovedStatus = "approved";
                        // break;
                    }
                }
            }
            $grnItemList[$itemKey]["parentGlId"] = $this->getInventoryItemParentGl($grnItem["itemId"]);
        }

        $insertgrnSql = 'INSERT INTO `' . ERP_GRN . '` SET 
                        `companyId`="' . $company_id . '",
                        `branchId`="' . $branch_id . '",
                        `locationId`="' . $location_id . '",
                        `pending_grn_id`="' . $multiple_id . '",
                        `functionalAreaId`="",
                        `grnCode`="' . $grnCode . '",
                        `grnType`="' . $grnType . '",
                        `grnPoNumber`="' . $grnPoNumber . '",
                        `po_date`="' . $po_date . '",
                        `vendorId`=' . $vendorId . ',
                        `vendorCode`="' . $vendorCode . '",
                        `vendorGstin`="' . $vendorGstin . '",
                        `vendorName`="' . $vendorName . '",
                        `vendorDocumentNo`="' . $documentNo . '",
                        `vendorDocumentDate`="' . $documentDate . '",
                        `postingDate`="' . $invoicePostingDate . '",
                        `dueDate`="' . $invoiceDueDate . '",
                        `dueDays`="' . $invoiceDueDays . '",
                        `paymentStatus`="1",
                        `dueAmt`="' . $totalInvoiceTotal . '",
                        `grnSubTotal`="' . $totalInvoiceSubTotal . '",
                        `grnTotalCgst`="' . $totalInvoiceCGST . '",
                        `grnTotalSgst`="' . $totalInvoiceSGST . '",
                        `grnTotalIgst`="' . $totalInvoiceIGST . '",
                        `grnTotalTds`="' . $totalTds . '",
                        `roundvalue`="' . $roundvalue . '",
                        `grnTotalAmount`="' . $totalInvoiceTotal . '",
                        `locationGstinStateName`="' . $locationGstinStateName . '",
                        `vendorGstinStateName`="' . $vendorGstinStateName . '",
                        `vendorDocumentFile`="' . $vendorDocumentFile . '",
                        `grnCreatedBy`="' . $created_by . '",
                        `grnUpdatedBy`="' . $updated_by . '",
                        `currency` = "' . $currency . '",
                        `conversion_rate` = "' . $rate . '",
                        `functional_area` = "' . $func . '" ,
                        `grnApprovedStatus`="' . $grnApprovedStatus . '"';

        $inserGrnObj = queryInsert($insertgrnSql);



        if ($inserGrnObj["status"] != "success") {
            return $inserGrnObj;
        } else {
            $grnId = $inserGrnObj["insertedId"];
            $errorsInGrnItemsAdd = 0;


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_GRN;
            $auditTrail['basicDetail']['column_name'] = 'grnId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $grnId;  // primary key
            $auditTrail['basicDetail']['document_number'] = $grnCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = $grnType . ' Creation';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insertgrnSql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';



            $auditTrail['action_data']['Basic Details']['grnCode'] = $grnCode;
            $auditTrail['action_data']['Basic Details']['grnType'] = $grnType;
            $auditTrail['action_data']['Basic Details']['grnPoNumber'] = $grnPoNumber;
            $auditTrail['action_data']['Basic Details']['po_date'] = $po_date;
            $auditTrail['action_data']['Basic Details']['vendorCode'] = $vendorCode;
            $auditTrail['action_data']['Basic Details']['vendorGstin'] = $vendorGstin;
            $auditTrail['action_data']['Basic Details']['vendorName'] = $vendorName;
            $auditTrail['action_data']['Basic Details']['vendorDocumentNo'] = $documentNo;
            $auditTrail['action_data']['Basic Details']['vendorDocumentDate'] = formatDateORDateTime($documentDate);
            $auditTrail['action_data']['Basic Details']['postingDate'] = formatDateORDateTime($invoicePostingDate);
            $auditTrail['action_data']['Basic Details']['dueDate'] = formatDateORDateTime($invoiceDueDate);
            $auditTrail['action_data']['Basic Details']['dueDays'] = $invoiceDueDays;
            $auditTrail['action_data']['Basic Details']['paymentStatus'] = "1";
            $auditTrail['action_data']['Basic Details']['dueAmt'] = $totalInvoiceTotal;
            $auditTrail['action_data']['Basic Details']['grnSubTotal'] = $totalInvoiceSubTotal;
            $auditTrail['action_data']['Basic Details']['grnTotalCgst'] = $totalInvoiceCGST;
            $auditTrail['action_data']['Basic Details']['grnTotalSgst'] = $totalInvoiceSGST;
            $auditTrail['action_data']['Basic Details']['grnTotalIgst'] = $totalInvoiceIGST;
            $auditTrail['action_data']['Basic Details']['grnTotalTds'] = $totalTds;
            $auditTrail['action_data']['Basic Details']['grnTotalAmount'] = $totalInvoiceTotal;
            $auditTrail['action_data']['Basic Details']['locationGstinStateName'] = $locationGstinStateName;
            $auditTrail['action_data']['Basic Details']['vendorGstinStateName'] = $vendorGstinStateName;
            $auditTrail['action_data']['Basic Details']['vendorDocumentFile'] = $vendorDocumentFile;
            $auditTrail['action_data']['Basic Details']['grnCreatedBy'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Basic Details']['grnUpdatedBy'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['Basic Details']['currency'] = $currency;
            $auditTrail['action_data']['Basic Details']['conversion_rate'] = $rate;
            $auditTrail['action_data']['Basic Details']['functional_area'] = $func;
            $auditTrail['action_data']['Basic Details']['grnApprovedStatus'] = $grnApprovedStatus;


            // if($grnType == "srn"){

            //     $costs = $INPUTS['cost_center'];
            //     foreach($costs as $cost){
            //         $costcenter = $cost['costcenter'];
            //         $costcenter_rate = $cost['rate'];
            //         $costcenter_id = $cost['costcenter_id'];
            //           //console($cost);
            //         $cost_center = queryInsert("INSERT INTO `erp_srn_costcenter_details` SET `srn_id` = $grnId, `costcenter_id`= $costcenter_id, `costcenter_percentage`=$costcenter_rate ");
            //     }
            // }

            // Insert all GRN items
            foreach ($grnItemList as $grnItem) {
                $oneItemId = $grnItem["itemId"] ?? 0;
                $oneItemCode = $grnItem["itemCode"];
                $oneItemHsn = $grnItem["itemHsn"];
                $oneItemName = addslashes($grnItem["itemName"]);
                $oneItemQty = $grnItem["itemQty"];
                $oneItemTax = $grnItem["itemTax"];
                $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                $oneItemTotalPrice = $grnItem["itemGrandTotalPrice"];
                $oneItemStorageLocationId = $grnItem["itemStorageLocationId"] ?? 0;
                $oneItemStocksQty = $grnItem["itemStockQty"] ?? 0;
                $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                $oneItemUOM = $grnItem["itemUOM"] ?? "";
                ///////////////////////////////////////////////////////////
                $oneItemCGST = $grnItem["itemCGST"] ?? "";
                $oneItemSGST = $grnItem["itemSGST"] ?? "";
                $oneItemIGST = $grnItem["itemIGST"] ?? "";
                $oneItemTDS = $grnItem["itemTds"] ?? "";
                $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                $oneItemInsertQuery = 'INSERT INTO `' . ERP_GRN_GOODS . '` 
                    SET `grnId`="' . $grnId . '",
                        `grnCode`="' . $grnCode . '",
                        `goodName`="' . $oneItemName . '",
                        `goodDesc`="",
                        `goodId`="' . $oneItemId . '",
                        `goodCode`="' . $oneItemCode . '",
                        `goodHsn`="' . $oneItemHsn . '",
                        `goodQty`="' . $oneItemQty . '",
                        `receivedQty`="' . $oneItemReceivedQty . '",
                        `unitPrice`="' . $oneItemUnitPrice . '",
                        `cgst`="' . $oneItemCGST . '", 
                        `sgst`="' . $oneItemSGST . '",
                        `igst`="' . $oneItemIGST . '",
                        `tds`="' . $oneItemTDS . '",
                        `goodstype`="' . $oneItemgoodsType . '",
                        `totalAmount`="' . $oneItemTotalPrice . '",
                        `itemStocksQty`="' . $oneItemStocksQty . '",
                        `itemUOM`="' . $oneItemUOM . '", 
                        `itemStorageLocation`=' . $oneItemStorageLocationId . ',
                        `grnType`="' . $grnType . '", 
                        `grnGoodCreatedBy`="' . $created_by . '",
                        `grnGoodUpdatedBy`="' . $updated_by . '"';

                $oneItemInsertObj = queryInsert($oneItemInsertQuery);
                if ($oneItemInsertObj["status"] != "success") {
                    $errorsInGrnItemsAdd++;
                    $lastgrnitem_id = $oneItemInsertObj['insertedId'];
                    if ($oneItemgoodsType == "service") {
                        foreach ($grnItem['cost_center'] as $costcenter) {
                            $costcenter = $costcenter['name'];
                            $costcenter_rate = $costcenter['rate'];
                            $costcenter_id = $costcenter['id'];
                            $cost_center = queryInsert("INSERT INTO `erp_srn_costcenter_details` SET `srn_id` = $grnId, `costcenter_id`= $costcenter_id, `costcenter_percentage`=$costcenter_rate ,`srn_item_id`=$lastgrnitem_id");
                        }
                    }
                } else {

                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['grnCode'] = $grnCode;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['goodName'] = $oneItemName;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['goodDesc'] = "";
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['goodCode'] = $oneItemCode;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['goodHsn'] = $oneItemHsn;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['goodQty'] = $oneItemQty;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['receivedQty'] = $oneItemReceivedQty;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['unitPrice'] = $oneItemUnitPrice;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['cgst'] = $oneItemCGST;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['sgst'] = $oneItemSGST;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['igst'] = $oneItemIGST;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['tds'] = $oneItemTDS;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['totalAmount'] = $oneItemTotalPrice;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['itemStocksQty'] = $oneItemStocksQty;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['itemUOM'] = $oneItemUOM;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['itemStorageLocation'] = $oneItemStorageLocationId;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['grnType'] = $grnType;
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['grnGoodCreatedBy'] = getCreatedByUser($created_by);
                    $auditTrail['action_data']['Goods Details'][$oneItemCode]['grnGoodUpdatedBy'] = getCreatedByUser($updated_by);
                }
            }


            // Row material stocks entry 
            // new update on 12-12-2022 18:35pm
            $errorsItemStockSummaryUpdate = 0;
            if ($errorsInGrnItemsAdd == 0) {
                foreach ($grnItemList as $grnItem) {
                    $oneItemId = $grnItem["itemId"];
                    $oneItemCode = $grnItem["itemCode"];
                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                    if ($oneItemgoodsType == "goods") {
                        $oneItemStocksQty = $grnItem["itemReceivedQty"] ?? 0.00; //500
                        $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50

                        $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId);

                        $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0; //3000.00
                        $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0; //50.00
                        $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                        $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                        $itemNewTotalPrice = (float)$prevTotalPrice + ($oneItemStocksQty * $oneItemUnitPrice); //(150000 + (500 * 50)) //175000
                        $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                        // if (is_nan($movingWeightedPrice)) {
                        //     $movingWeightedPrice = 0;
                        // }

                        // if ($goodStockSummaryCheckSql["numRows"] > 0) {

                        //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;

                        //     // $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                        //     // if ($checkfgrm["data"]["storage_location_material_type"] == "RM" && $checkfgrm["data"]["storage_location_type"] == "RM-WH") {
                        //     //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `rmWhOpen`=`rmWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                        //     // } elseif ($checkfgrm["data"]["storage_location_material_type"] == "FG" && $checkfgrm["data"]["storage_location_type"] == "FG-WH") {
                        //     //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `fgWhOpen`=`fgWhOpen`+' . $oneItemStocksQty . ', `itemTotalQty`=' . $itemNewTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;
                        //     // }

                        //     $goodStockInsertObj = queryUpdate($goodStockInserSql);
                        //     if ($goodStockInsertObj["status"] != "success") {
                        //         $errorsItemStockSummaryUpdate++;
                        //         // return $goodStockInserSql;
                        //         //  $goodStockInsertObj;
                        //     }

                        //     queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $movingWeightedPrice . ',`createdBy`="' . $created_by . '"');
                        // } else {

                        //     $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;

                        //     // $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                        //     // if ($checkfgrm["data"]["storage_location_material_type"] == "RM" && $checkfgrm["data"]["storage_location_type"] == "RM-WH") {
                        //     //     $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `rmWhOpen`=' . $oneItemStocksQty . ', `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;
                        //     // } elseif ($checkfgrm["data"]["storage_location_material_type"] == "FG" && $checkfgrm["data"]["storage_location_type"] == "FG-WH") {
                        //     //     $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `fgWhOpen`=' . $oneItemStocksQty . ', `itemTotalQty` = ' . $oneItemStocksQty . ', `movingWeightedPrice`=' . $oneItemUnitPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $oneItemId;
                        //     // }

                        //     $goodStockInsertObj = queryInsert($goodStockInserSql);

                        //     if ($goodStockInsertObj["status"] != "success") {
                        //         $errorsItemStockSummaryUpdate++;
                        //     } else {
                        //         ///---------------------------------Audit Log Start---------------------
                        //         $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        //         $auditTrailSummry = array();
                        //         $auditTrailSummry['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                        //         $auditTrailSummry['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                        //         $auditTrailSummry['basicDetail']['column_name'] = 'itemId'; // Primary key column
                        //         $auditTrailSummry['basicDetail']['document_id'] = $oneItemId;  //     primary key
                        //         $auditTrailSummry['basicDetail']['document_number'] = $oneItemCode;
                        //         $auditTrailSummry['basicDetail']['action_code'] = $action_code;
                        //         $auditTrailSummry['basicDetail']['action_referance'] = $grnCode;
                        //         $auditTrailSummry['basicDetail']['action_title'] = 'Item Stock added';  //Action comment
                        //         $auditTrailSummry['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                        //         $auditTrailSummry['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        //         $auditTrailSummry['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        //         $auditTrailSummry['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        //         $auditTrailSummry['basicDetail']['action_sqlQuery'] = base64_encode($goodStockInserSql);
                        //         $auditTrailSummry['basicDetail']['others'] = '';
                        //         $auditTrailSummry['basicDetail']['remark'] = '';



                        //         $auditTrailSummry['action_data']['Summary']['itemTotalQty'] = $itemNewTotalQty;
                        //         $auditTrailSummry['action_data']['Summary']['movingWeightedPrice'] = $movingWeightedPrice;
                        //         $auditTrailSummry['action_data']['Summary']['rmWhOpen'] = $oneItemStocksQty;

                        //         $auditTrailreturn = generateAuditTrail($auditTrailSummry);
                        //     }

                        //     queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $oneItemId . ',`itemCode`="' . $oneItemCode . '",`movingAveragePrice`=' . $oneItemUnitPrice . ',`createdBy`="' . $created_by . '"');
                        // }
                    }
                }
            }

            // Row material stocks entry log entry
            $errorsItemStockLogInsert = 0;
            if ($errorsItemStockSummaryUpdate == 0) {

                foreach ($grnItemList as $grnItem) {
                    $oneItemId = $grnItem["itemId"];
                    $oneItemCode = $grnItem["itemCode"];
                    $oneItemHsn = $grnItem["itemHsn"];
                    $oneItemName = addslashes($grnItem["itemName"]);
                    $oneItemQty = $grnItem["itemQty"];
                    $oneItemUomId = $grnItem["itemUOMID"] ?? "";
                    $oneItemTax = $grnItem["itemTax"];
                    $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                    $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                    $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                    $oneItemStocksQty = $grnItem["itemStockQty"] ?? $oneItemQty;
                    $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                    $item_query = queryGet('SELECT * FROM erp_inventory_items WHERE itemId="' . $oneItemId . '"', false);
                    $oneItemgoodsGroup = $item_query["data"]["goodsGroup"];

                    $logRef = $grnCode;


                    if ($oneItemgoodsType == "goods") {

                        if (isset($grnItem["activateBatch"]) && $grnItem["activateBatch"] == 1) {
                            foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                $batchNumber = $multipleBatch["batchNumber"];
                                $batchQuantity = $multipleBatch["quantity"];
                                $bin = $multipleBatch["bin"];
                                $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                $rack_id = $layer_query["data"]["rack_id"] ?? 0;


                                if (is_null($batchNumber) || $batchNumber == "") {
                                    $batchNumber = $logRef;
                                }

                                $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                $stockLogInsertSql = $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`parentId`='.$grnId.',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $batchNumber . '", `refNumber`="' . $grnCode . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $batchNumber . '", `qty` = "' . $batchQuantity . '", `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`= "' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                $binInsertObj = queryInsert($binMappingInsert);
                                // return $stockLogInsertObj;

                                if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                    $errorsItemStockLogInsert++;
                                }
                            }
                        } else {
                            foreach ($grnItem["multipleBatch"] as $multipleBatch) {
                                // $batchNumber = $multipleBatch["batchNumber"];
                                $batchQuantity = $multipleBatch["quantity"];
                                $bin = $multipleBatch["bin"];
                                $bin_query = queryGet('SELECT * FROM `erp_storage_bin` WHERE `bin_id`=' . $bin, false);
                                $layer_id = $bin_query["data"]["layer_id"] ?? 0;
                                $layer_query = queryGet('SELECT * FROM `erp_layer` WHERE `layer_id`=' . $layer_id, false);
                                $rack_id = $layer_query["data"]["rack_id"] ?? 0;

                                $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                                $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];
                                $warehouse_id = $checkfgrm["data"]["warehouse_id"];

                                $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`parentId`='.$grnId.',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $batchQuantity . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $grnCode . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                                $stockLogInsertObj = queryInsert($stockLogInsertSql);

                                $binMappingInsert = 'INSERT INTO `erp_bin_mapping` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`warehouse`=' . $warehouse_id . ',`st_loc`=' . $oneItemStorageLocationId . ', `rack`=' . $rack_id . ', `layer`=' . $layer_id . ', `bin`=' . $bin . ', `batch`="' . $logRef . '", `qty` = ' . $batchQuantity . ', `item_id`=' . $oneItemId . ',`item_group`=' . $oneItemgoodsGroup . ', `item_name`="' . $oneItemName . '",`item_code`="' . $oneItemCode . '", `uom` = ' . $oneItemUomId;
                                $binInsertObj = queryInsert($binMappingInsert);
                                // return $stockLogInsertObj;
                                // console($binInsertObj);

                                if ($stockLogInsertObj["status"] != "success" && $binInsertObj["status"] != "success") {
                                    $errorsItemStockLogInsert++;
                                }
                            }
                        }
                    }



                    // if ($oneItemgoodsType == "goods") {

                    //     $checkfgrm = queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `storage_location_id`=' . $oneItemStorageLocationId, false);

                    //     $st_loc_slug = $checkfgrm["data"]["storageLocationTypeSlug"];

                    //     $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="' . $st_loc_slug . '",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $oneItemStocksQty . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $logRef . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                    //     // if ($checkfgrm["data"]["storage_location_material_type"] == "RM" && $checkfgrm["data"]["storage_location_type"] == "RM-WH") {

                    //     //     $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="rmWhOpen",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $oneItemStocksQty . ',`itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $logRef . '", `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';

                    //     // } elseif ($checkfgrm["data"]["storage_location_material_type"] == "FG" && $checkfgrm["data"]["storage_location_type"] == "FG-WH") {

                    //     //     $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="fgWhOpen",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $oneItemStocksQty . ', `bornDate`="' . $invoicePostingDate . '", `postingDate`="' . $invoicePostingDate . '", `itemUom`=' . $oneItemUomId . ',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="GRN", `logRef`="' . $logRef . '", `refNumber`="' . $logRef . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';
                    //     // }

                    //     $stockLogInsertObj = queryInsert($stockLogInsertSql);



                    //     if ($stockLogInsertObj["status"] != "success") {
                    //         $errorsItemStockLogInsert++;
                    //     }
                    // }
                }
            }

            if ($errorsInGrnItemsAdd == 0 && $errorsItemStockSummaryUpdate == 0 && $errorsItemStockLogInsert == 0) {
                if ($grnApprovedStatus == "approved") {

                    if ($grnType == 'grn') {
                        $accslug = "grn";
                        $remarks = "GRN By OCR " . $grnCode . " " . $extra_remark;
                    } else {
                        $accslug = "srn";
                        $remarks = "SRN By OCR " . $grnCode . " " . $extra_remark;
                    }



                    $grnPostingAccountingData = [
                        "documentNo" => $documentNo,
                        "documentDate" => $documentDate,
                        "invoiceDueDate" => $invoiceDueDate,
                        "invoicePostingDate" => $invoicePostingDate,
                        "referenceNo" => $grnCode,
                        "journalEntryReference" => 'Purchase',
                        "remarks" => addslashes($remarks),
                        "grnItemList" =>  $grnItemList,
                        "party_code" => $vendorCode,
                        "party_name" =>  $vendorName
                    ];
                    if ($grnType == 'grn') {
                        $grnAccPostingObj = $this->grnAccountingPosting($grnPostingAccountingData, $accslug, $grnId);
                    } else {
                        $grnAccPostingObj = $this->srnAccountingPosting($grnPostingAccountingData, $accslug, $grnId);
                    }
                    if ($grnAccPostingObj["status"] == "success" && $grnAccPostingObj["journalId"] != "") {

                        $queryObj = queryUpdate('UPDATE `' . ERP_GRN . '` SET `grnPostingJournalId`=' . $grnAccPostingObj["journalId"] . ' WHERE `grnId`=' . $grnId);
                        if ($queryObj['status'] == "success") {
                            if ($errorsInGrnItemsAdd == 0) {
                                foreach ($grnItemList as $grnItem) {

                                    $oneItemId = $grnItem["itemId"];
                                    $oneItemCode = $grnItem["itemCode"];
                                    $oneItemgoodsType = $grnItem["itemInvoiceGoodsType"] ?? "";

                                    if ($oneItemgoodsType == "goods") {
                                        $oneItemStocksQty = $grnItem["itemReceivedQty"] ?? 0.00; //500
                                        $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50
                                       
                                        $mwp = calculateNewMwp($oneItemId, $oneItemStocksQty, $oneItemUnitPrice, "GRN");
                                    }
                                }
                            }
                        }
                    }else{
                        $logAccFailedResponce = $this->failedAccController->logAccountingFailure($grnId, $grnType);
                    }
                }

                //UPDATE PO
                if (isset($INPUTS["poStatus"]) && $INPUTS["poStatus"] != "" && $INPUTS["poStatus"] == 1) {
                    //Close PO
                    $close = 10;
                    $update_po = queryUpdate('UPDATE `erp_branch_purchase_order` SET `po_status`=' . $close . ' WHERE `po_number`="' . $grnPoNumber . '"');
                } elseif (isset($INPUTS["poStatus"]) && $INPUTS["poStatus"] != "" && $INPUTS["poStatus"] == 0) {

                    foreach ($INPUTS["poItemId"] as $key => $itemQtyPO) {

                        if ($itemQtyPO < 0) {
                            $itemQtyPO = 0;
                        }

                        queryUpdate('UPDATE `erp_branch_purchase_order_items` SET `remainingQty`=' . $itemQtyPO . ' WHERE `po_item_id`="' . $key . '"');
                    }

                    //All Item From PO Check Remaining Quantity as 0 and make it close
                    $po_id = $check_po_exists["data"]["po_id"];
                    $po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` WHERE remainingQty > 0 AND po_id = '" . $po_id . "'", true);

                    if ($po_item["numRows"] == 0) {
                        //Update to close
                        $close = 10;
                        queryUpdate('UPDATE `erp_branch_purchase_order` SET `po_status`=' . $close . ' WHERE `po_number`="' . $grnPoNumber . '"');
                    }
                } elseif (isset($INPUTS["poStatus"]) && $INPUTS["poStatus"] != "" && $INPUTS["poStatus"] == 9999) {
                    foreach ($INPUTS["poItemId"] as $key => $itemQtyPO) {
                        queryUpdate('UPDATE `erp_branch_purchase_order_items` SET `remainingQty`=' . $itemQtyPO . ' WHERE `po_item_id`="' . $key . '"');
                    }
                    $close = 10;
                    $update_po = queryUpdate('UPDATE `erp_branch_purchase_order` SET `po_status`=' . $close . ' WHERE `po_number`="' . $grnPoNumber . '"');
                }

                //UPDATE
                $updatemultiple_grn = queryUpdate("UPDATE `erp_grn_multiple` SET `status`= '1' WHERE `grn_mul_id`='" . $multiple_id . "'");

                $auditTrailreturn = generateAuditTrail($auditTrail);

                if ($grnType == 'grn') {

                    return [
                        "status" => "success",
                        "message" => ($grnApprovedStatus == "pending") ? "GRN posted successfully, waiting for 'approval'" : "GRN posted successfully." . $grnCode,
                        "acc" => $grnAccPostingObj,
                        "goodStockInsertObj" => $goodStockInsertObj,
                        "goodStockInserSql" => $goodStockInserSql,
                        "grnPostingAccountingData" => $grnPostingAccountingData
                    ];
                } else {
                    return [
                        "status" => "success",
                        "message" => ($grnApprovedStatus == "pending") ? "SRN posted successfully, waiting for 'approval'" : "SRN posted successfully." . $grnCode,
                        "acc" => $grnAccPostingObj,
                        "goodStockInsertObj" => $goodStockInsertObj,
                        "goodStockInserSql" => $goodStockInserSql,
                        "grnPostingAccountingData" => $grnPostingAccountingData
                    ];
                }
            } else {
                return [
                    "status" => "warning",
                    "sql" => $oneItemInsertQuery,
                    "message" => "GRN posted failed, try again! " . $errorsInGrnItemsAdd . "," . $errorsItemStockSummaryUpdate . "," . $errorsItemStockLogInsert,
                    "grnPostingAccountingData" => ''
                ];
            }
        }
    }


    function createInvoice($INPUTS)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $returnData = [];
        // return $INPUTS;
        // console($INPUTS);
        // exit();

        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "grnItemList" => "array"
        ]);

        // "vendorGstin" => "required",
        // "totalInvoiceCGST" => "required",
        // "totalInvoiceSGST" => "required",
        // "totalInvoiceIGST" => "required",
        // "totalInvoiceSubTotal" => "required",
        // "totalInvoiceTotal" => "required",

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $grnPoNumber = $INPUTS["invoicePoNumber"];
        $grnCode = $INPUTS["grnCode"];
        $documentNo = $INPUTS["documentNo"];
        $documentDate = $INPUTS["documentDate"];
        $invoicePostingDate = $INPUTS["invoicePostingDate"];
        $invoiceDueDate = $INPUTS["invoiceDueDate"];
        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        $vendorId = $INPUTS["vendorId"];
        $vendorCode = $INPUTS["vendorCode"];
        $vendorName = addslashes($INPUTS["vendorName"]);
        $vendorGstin = $INPUTS["vendorGstin"];
        $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
        $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
        $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
        $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
        $totalInvoiceTotal = $INPUTS["tdAdjustedTotalval"];
        $locationGstinStateName = $INPUTS["locationGstinStateName"];
        $vendorGstinStateName = $INPUTS["vendorGstinStateName"];
        $vendorDocumentFile = $INPUTS["vendorDocumentFile"];
        $grnApprovedStatus = $INPUTS["grnApprovedStatus"];
        $totalInvoiceTDS = $INPUTS["totalInvoiceTDS"];
        $totalInvoiceTCS = $INPUTS["totalInvoiceTCS"] ?? 0;
        $grnItemList = $INPUTS["grnItemList"];
        $grnType = $INPUTS["grnType"];
        $rcm = $INPUTS["rcm"];
        $id = $INPUTS["id"];

        // checking Previous accounting impact
        $checkSql = "SELECT grnPostingJournalId FROM " . ERP_GRN . " as g WHERE g.grnId='$id' AND g.companyId='$company_id' AND g.branchId='$branch_id' AND g.locationId='$location_id' AND grnStatus='active' ";
        $checkObj = queryGet($checkSql);
        if ($checkObj['status'] == "success" && $checkObj['numRows'] > 0) {
            $checkData = $checkObj["data"];
            $grnPostingJournalId = $checkData['grnPostingJournalId'];

            if ($grnPostingJournalId == 0 || $grnPostingJournalId == '' || $grnPostingJournalId == null) {
                return ['status' => 'error', 'message' => 'Accounting document not found. Cannot proceed further.','checkSql'=>$checkSql];
                exit();
            }
        } else {
            return ['status' => 'error', 'message' => 'Something Went Wrong !','checkSql'=>$checkSql];
            exit();
        }

        $grnIvCode = "";

        $roundvalue = $_POST['final_roundoff'];

        $roundOffGL = $roundvalue;

        // after round off
        $totalInvoiceTotal = $totalInvoiceTotal;


        if ($grnType == "grn") {
            $lastgrn = queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="grn") ORDER BY `grnId` DESC LIMIT 1');
            // $grnIvCode  = getGRNIVSerialNumber($lastgrn["data"]["grnCode"] ?? "");
            $grnIvCode  = getGRNIVSerialNumber($grnCode);
        } else {
            $lastgrn = queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="srn") ORDER BY `grnId` DESC LIMIT 1');
            // $grnIvCode  = getSRNIVSerialNumber($lastgrn["data"]["grnCode"] ?? "");
            $grnIvCode  = getSRNIVSerialNumber($grnCode);
        }
        $querryGrn = 'SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="grn") ORDER BY `grnId` DESC LIMIT 1';


        if (isset($rcm) && $rcm == 1) {
            $rcm_value = 1;
        } else {
            $rcm_value = 0;
        }

        $grnIvSql = 'INSERT INTO `erp_grninvoice` SET 
                        `companyId`="' . $company_id . '",
                        `branchId`="' . $branch_id . '",
                        `locationId`="' . $location_id . '",
                        `functionalAreaId`="",
                        `grnId`="' . $id . '",
                        `grnCode`="' . $grnCode . '",
                        `grnPoNumber`="' . $grnPoNumber . '",
                        `grnIvCode`="' . $grnIvCode . '",
                        `grnType`="' . $grnType . '",
                        `vendorId`=' . $vendorId . ',
                        `vendorCode`="' . $vendorCode . '",
                        `vendorGstin`="' . $vendorGstin . '",
                        `vendorName`="' . $vendorName . '",
                        `vendorDocumentNo`="' . $documentNo . '",
                        `vendorDocumentDate`="' . $documentDate . '",
                        `postingDate`="' . $invoicePostingDate . '",
                        `dueDate`="' . $invoiceDueDate . '",
                        `dueDays`="' . $invoiceDueDays . '",
                        `paymentStatus`="15",
                        `dueAmt`="' . $totalInvoiceTotal . '",
                        `grnSubTotal`="' . $totalInvoiceSubTotal . '",
                        `grnTotalCgst`="' . $totalInvoiceCGST . '",
                        `grnTotalSgst`="' . $totalInvoiceSGST . '",
                        `grnTotalIgst`="' . $totalInvoiceIGST . '",
                        `grnTotalTds`="' . $totalInvoiceTDS . '",
                        `grnTotalTcs`="' . $totalInvoiceTCS . '",
                        `grnTotalAmount`="' . $totalInvoiceTotal . '",
                        `rcm_enabled`="' . $rcm_value . '",
                        `locationGstinStateName`="' . $locationGstinStateName . '",
                        `vendorGstinStateName`="' . $vendorGstinStateName . '",
                        `vendorDocumentFile`="' . $vendorDocumentFile . '",
                        `roundoff`="' . $roundOffGL . '",
                        `grnCreatedBy`="' . $created_by . '",
                        `grnUpdatedBy`="' . $updated_by . '",
                        `grnApprovedStatus`="' . $grnApprovedStatus . '"';
        $inserGrnObj = queryInsert($grnIvSql);


        if ($inserGrnObj["status"] != "success") {
            return $inserGrnObj;
        } else {
            $grnIVId = $inserGrnObj["insertedId"];
            $errorsInGrnItemsAdd = 0;

            $grnInvoice = queryGet('SELECT * FROM `erp_grninvoice` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnIvId`=' . $grnIVId . ')');
            $grnIvCode  = $grnInvoice["data"]["grnIvCode"];

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_grninvoice';
            $auditTrail['basicDetail']['column_name'] = 'grnIvId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $grnIVId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $grnIvCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = $grnCode;
            $auditTrail['basicDetail']['action_title'] = 'IV Posting ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($grnIvSql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['IV Details']['GRN_Code'] = $grnCode;
            $auditTrail['action_data']['IV Details']['GRN Po Number'] = $grnPoNumber;
            $auditTrail['action_data']['IV Details']['GRN Iv Code'] = $grnIvCode;
            $auditTrail['action_data']['IV Details']['GRN_Type'] = $grnType;
            $auditTrail['action_data']['IV Details']['Vendor_Code'] = $vendorCode;
            $auditTrail['action_data']['IV Details']['Vendor_Gstin'] = $vendorGstin;
            $auditTrail['action_data']['IV Details']['Vendor_Name'] = $vendorName;
            $auditTrail['action_data']['IV Details']['Vendor Document No'] = $documentNo;
            $auditTrail['action_data']['IV Details']['Vendor Document Date'] = formatDateORDateTime($documentDate);
            $auditTrail['action_data']['IV Details']['Posting_Date'] = formatDateORDateTime($invoicePostingDate);
            $auditTrail['action_data']['IV Details']['Due_Date'] = formatDateORDateTime($invoiceDueDate);
            $auditTrail['action_data']['IV Details']['Due_Days'] = $invoiceDueDays;
            $auditTrail['action_data']['IV Details']['Payment_Status'] = "15";
            $auditTrail['action_data']['IV Details']['Due_Amt'] = decimalValuePreview($totalInvoiceTotal);
            $auditTrail['action_data']['IV Details']['GRN Sub Total'] = decimalValuePreview($totalInvoiceSubTotal);
            $auditTrail['action_data']['IV Details']['GRN Total CGST'] = decimalValuePreview($totalInvoiceCGST);
            $auditTrail['action_data']['IV Details']['GRN Total SGST'] = decimalValuePreview($totalInvoiceSGST);
            $auditTrail['action_data']['IV Details']['GRN Total IGST'] = decimalValuePreview($totalInvoiceIGST);
            $auditTrail['action_data']['IV Details']['GRN Total TDS'] = decimalValuePreview($totalInvoiceTDS);
            $auditTrail['action_data']['IV Details']['GRN Total Amount'] = decimalValuePreview($totalInvoiceTotal);
            $auditTrail['action_data']['IV Details']['Location Gstin State Name'] = $locationGstinStateName;
            $auditTrail['action_data']['IV Details']['Vendor Gstin State Name'] = $vendorGstinStateName;
            $auditTrail['action_data']['IV Details']['Vendor_Document_File'] = $vendorDocumentFile;
            $auditTrail['action_data']['IV Details']['GRN Created By'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['IV Details']['GRN Updated By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['IV Details']['GRN Approved Status'] = $grnApprovedStatus;



            // Insert all GRN items
            foreach ($grnItemList as $grnItem) {
                $oneItemId = $grnItem["itemId"];
                $oneItemCode = $grnItem["itemCode"];
                $oneItemHsn = $grnItem["itemHsn"];
                $oneItemName = $grnItem["itemName"];
                $oneItemQty = $grnItem["itemQty"];
                $oneItemTax = $grnItem["itemTax"];
                $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                $oneItemStocksQty = $grnItem["itemStockQty"];
                $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                ///////////////////////////////////////////////////////////
                $oneItemCGST = $grnItem["itemCGST"] ?? "";
                $oneItemSGST = $grnItem["itemSGST"] ?? "";
                $oneItemIGST = $grnItem["itemIGST"] ?? "";
                $oneItemTDS = $grnItem["itemTDS"] ?? "";
                $itemUom = $grnItem["itemUOM"] ?? "";

                $oneItemInsertQuery = 'INSERT INTO `erp_grninvoice_goods` 
                                        SET `grnId`="' . $id . '",
                                            `grnCode`="' . $grnCode . '",
                                            `goodName`="' . $oneItemName . '",
                                            `goodDesc`="",
                                            `grnIvCode`="' . $grnIvCode . '",
                                            `grnIvId`="' . $grnIVId . '",
                                            `goodId`="' . $oneItemId . '",
                                            `tds`="' . $oneItemTDS . '",
                                            `grnType`="' . $grnType . '",
                                            `goodCode`="' . $oneItemCode . '",
                                            `goodHsn`="' . $oneItemHsn . '",
                                            `goodQty`="' . $oneItemQty . '",
                                            `receivedQty`="' . $oneItemReceivedQty . '",
                                            `unitPrice`="' . $oneItemUnitPrice . '",
                                            `cgst`="' . $oneItemCGST . '", 
                                            `sgst`="' . $oneItemSGST . '",
                                            `igst`="' . $oneItemIGST . '", 
                                            `totalAmount`="' . $oneItemTotalPrice . '",  
                                            `itemStocksQty`="' . $oneItemStocksQty . '",
                                            `itemUOM`="' . $itemUom . '", 
                                            `itemStorageLocation`=' . $oneItemStorageLocationId . ', 
                                            `grnGoodCreatedBy`="' . $created_by . '",
                                            `grnGoodUpdatedBy`="' . $updated_by . '"';

                $oneItemInsertObj = queryInsert($oneItemInsertQuery);
                if ($oneItemInsertObj["status"] != "success") {
                    $errorsInGrnItemsAdd++;
                } else {

                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN_Code'] = $grnCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Name'] = $oneItemName;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Desc'] = "";
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Iv Code'] = $grnIvCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['TDS'] = decimalValuePreview($oneItemTDS);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN_Type'] = $grnType;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Code'] = $oneItemCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_HSN'] = $oneItemHsn;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Qty'] = decimalQuantityPreview($oneItemQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Received_Qty'] = decimalQuantityPreview($oneItemReceivedQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Unit_Price'] = decimalValuePreview($oneItemUnitPrice);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['CGST'] = decimalValuePreview($oneItemCGST);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['SGST'] = decimalValuePreview($oneItemSGST);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['IGST'] = decimalValuePreview($oneItemIGST);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Total_Amount'] = decimalValuePreview($oneItemTotalPrice);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Item Stocks Qty'] = decimalQuantityPreview($oneItemStocksQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Good Created By'] = getCreatedByUser($created_by);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Good Updated By'] = getCreatedByUser($updated_by);
                }
            }

            $updatemultiple_grn = queryUpdate("UPDATE `erp_grn` SET `iv_status`= '1' WHERE `grnId`='" . $id . "'");

            $auditTrailreturn = generateAuditTrail($auditTrail);

            return [
                "status" => "success",
                "message" => "Invoice posted successfully." . $grnIvCode,
                "grnIVId" => $grnIVId,
                "data" => $oneItemInsertObj
            ];

            // return [
            //     "status"=> "warning",
            //     "message"=> "GRN posted failed, try again! ".$errorsInGrnItemsAdd.",".$errorsItemStockSummaryUpdate.",".$errorsItemStockLogInsert
            // ];
        }
    }

    function createInvoice2($INPUTS)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $companyCountry;

        $returnData = [];
        // return $INPUTS;
        // console($INPUTS);
        // exit();

        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "grnItemList" => "array"
        ]);

        // "vendorGstin" => "required",
        // "totalInvoiceCGST" => "required",
        // "totalInvoiceSGST" => "required",
        // "totalInvoiceIGST" => "required",
        // "totalInvoiceSubTotal" => "required",
        // "totalInvoiceTotal" => "required",

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $grnPoNumber = $INPUTS["invoicePoNumber"];
        $grnCode = $INPUTS["grnCode"];
        $documentNo = $INPUTS["documentNo"];
        $documentDate = $INPUTS["documentDate"];
        $invoicePostingDate = $INPUTS["invoicePostingDate"];
        $invoiceDueDate = $INPUTS["invoiceDueDate"];
        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        $vendorId = $INPUTS["vendorId"];
        $vendorCode = $INPUTS["vendorCode"];
        $vendorName = addslashes($INPUTS["vendorName"]);
        $vendorGstin = $INPUTS["vendorGstin"];
        $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"] ?? "0.0";
        $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"] ?? "0.0";
        $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"] ?? "0.0";
        $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
        $totalInvoiceTotal = $INPUTS["tdAdjustedTotalval"];
        $locationGstinStateName = $INPUTS["locationGstinStateName"];
        $vendorGstinStateName = $INPUTS["vendorGstinStateName"];
        $vendorDocumentFile = $INPUTS["vendorDocumentFile"];
        $grnApprovedStatus = $INPUTS["grnApprovedStatus"];
        $totalInvoiceTDS = $INPUTS["totalInvoiceTDS"];
        $totalInvoiceTCS = $INPUTS["totalInvoiceTCS"] ?? 0;
        $grnItemList = $INPUTS["grnItemList"];
        $grnType = $INPUTS["grnType"];
        $rcm = $INPUTS["rcm"];
        $id = $INPUTS["id"];
        $taxComponents = json_encode($INPUTS['totalInvoiceGrnd']);

        $grnIvCode = "";

        $roundvalue = $_POST['final_roundoff'];

        $roundOffGL = $roundvalue;

        // after round off
        $totalInvoiceTotal = $totalInvoiceTotal;




        if ($grnType == "grn") {
            $lastgrn = queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="grn") ORDER BY `grnId` DESC LIMIT 1');
            // $grnIvCode  = getGRNIVSerialNumber($lastgrn["data"]["grnCode"] ?? "");
            $grnIvCode  = getGRNIVSerialNumber($grnCode);
        } else {
            $lastgrn = queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="srn") ORDER BY `grnId` DESC LIMIT 1');
            // $grnIvCode  = getSRNIVSerialNumber($lastgrn["data"]["grnCode"] ?? "");
            $grnIvCode  = getSRNIVSerialNumber($grnCode);
        }

        $querryGrn = 'SELECT * FROM `' . ERP_GRN . '` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="grn") ORDER BY `grnId` DESC LIMIT 1';


        if (isset($rcm) && $rcm == 1) {
            $rcm_value = 1;
        } else {
            $rcm_value = 0;
        }

        if ($companyCountry != 103) {
            $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendorId AND `vendor_business_primary_flag` = 1");
            $b_row = $b_places['data'];
            $vendorGstinStateName = $b_row['vendor_business_state'];
            $venderabn = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendorId");

            $abn = $venderabn['data'];
            $vendorGstin = $abn['vendor_gstin'];
        }
        $grnIvSql = 'INSERT INTO `erp_grninvoice` SET 
                        `companyId`="' . $company_id . '",
                        `branchId`="' . $branch_id . '",
                        `locationId`="' . $location_id . '",
                        `functionalAreaId`="",
                        `grnId`="' . $id . '",
                        `grnCode`="' . $grnCode . '",
                        `grnPoNumber`="' . $grnPoNumber . '",
                        `grnIvCode`="' . $grnIvCode . '",
                        `grnType`="' . $grnType . '",
                        `vendorId`=' . $vendorId . ',
                        `vendorCode`="' . $vendorCode . '",
                        `vendorGstin`="' . $vendorGstin . '",
                        `vendorName`="' . $vendorName . '",
                        `vendorDocumentNo`="' . $documentNo . '",
                        `vendorDocumentDate`="' . $documentDate . '",
                        `postingDate`="' . $invoicePostingDate . '",
                        `dueDate`="' . $invoiceDueDate . '",
                        `dueDays`="' . $invoiceDueDays . '",
                        `paymentStatus`="15",
                        `dueAmt`="' . $totalInvoiceTotal . '",
                        `grnSubTotal`="' . $totalInvoiceSubTotal . '",
                        `grnTotalCgst`="' . $totalInvoiceCGST . '",
                        `grnTotalSgst`="' . $totalInvoiceSGST . '",
                        `grnTotalIgst`="' . $totalInvoiceIGST . '",
                        `grnTotalTds`="' . $totalInvoiceTDS . '",
                        `taxComponents`=' . $taxComponents . ',
                        `grnTotalTcs`="' . $totalInvoiceTCS . '",
                        `grnTotalAmount`="' . $totalInvoiceTotal . '",
                        `rcm_enabled`="' . $rcm_value . '",
                        `locationGstinStateName`="' . $locationGstinStateName . '",
                        `vendorGstinStateName`="' . $vendorGstinStateName . '",
                        `vendorDocumentFile`="' . $vendorDocumentFile . '",
                        `roundoff`="' . $roundOffGL . '",
                        `grnCreatedBy`="' . $created_by . '",
                        `grnUpdatedBy`="' . $updated_by . '",
                        `grnApprovedStatus`="' . $grnApprovedStatus . '"';

        $inserGrnObj = queryInsert($grnIvSql);


        if ($inserGrnObj["status"] != "success") {
            return $inserGrnObj;
        } else {
            $grnIVId = $inserGrnObj["insertedId"];
            $errorsInGrnItemsAdd = 0;

            $grnInvoice = queryGet('SELECT * FROM `erp_grninvoice` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnIvId`=' . $grnIVId . ')');
            $grnIvCode  = $grnInvoice["data"]["grnIvCode"];

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_grninvoice';
            $auditTrail['basicDetail']['column_name'] = 'grnIvId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $grnIVId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $grnIvCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = $grnCode;
            $auditTrail['basicDetail']['action_title'] = 'IV Posting ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($grnIvSql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['IV Details']['GRN_Code'] = $grnCode;
            $auditTrail['action_data']['IV Details']['GRN Po Number'] = $grnPoNumber;
            $auditTrail['action_data']['IV Details']['GRN Iv Code'] = $grnIvCode;
            $auditTrail['action_data']['IV Details']['GRN_Type'] = $grnType;
            $auditTrail['action_data']['IV Details']['Vendor_Code'] = $vendorCode;
            $auditTrail['action_data']['IV Details']['Vendor_Gstin'] = $vendorGstin;
            $auditTrail['action_data']['IV Details']['Vendor_Name'] = $vendorName;
            $auditTrail['action_data']['IV Details']['Vendor Document No'] = $documentNo;
            $auditTrail['action_data']['IV Details']['Vendor Document Date'] = formatDateORDateTime($documentDate);
            $auditTrail['action_data']['IV Details']['Posting_Date'] = formatDateORDateTime($invoicePostingDate);
            $auditTrail['action_data']['IV Details']['Due_Date'] = formatDateORDateTime($invoiceDueDate);
            $auditTrail['action_data']['IV Details']['Due_Days'] = $invoiceDueDays;
            $auditTrail['action_data']['IV Details']['Payment_Status'] = "15";
            $auditTrail['action_data']['IV Details']['Due_Amt'] = decimalValuePreview($totalInvoiceTotal);
            $auditTrail['action_data']['IV Details']['GRN Sub Total'] = decimalValuePreview($totalInvoiceSubTotal);

            if ($companyCountry == 103) {
                $auditTrail['action_data']['IV Details']['GRN Total CGST'] = decimalValuePreview($totalInvoiceCGST);
                $auditTrail['action_data']['IV Details']['GRN Total SGST'] = decimalValuePreview($totalInvoiceSGST);
                $auditTrail['action_data']['IV Details']['GRN Total IGST'] = decimalValuePreview($totalInvoiceIGST);
            } else {
                $decodedTaxComponents = json_decode($taxComponents, true);
            
                if (!empty($decodedTaxComponents)) {
                    foreach ($decodedTaxComponents as $component) {
                        $gstType = $component['gstType'] ?? '';
                        $taxAmount = $component['taxAmount'] ?? 0;
            
                        if ($gstType !== '') {
                            $auditTrail['action_data']['IV Details']["GRN Total " . strtoupper($gstType)] = decimalValuePreview($taxAmount);
                        }
                    }
                }
            }
            
            $auditTrail['action_data']['IV Details']['GRN Total TDS'] = decimalValuePreview($totalInvoiceTDS);
            $auditTrail['action_data']['IV Details']['GRN Total Amount'] = decimalValuePreview($totalInvoiceTotal);
            $auditTrail['action_data']['IV Details']['Location Gstin State Name'] = $locationGstinStateName;
            $auditTrail['action_data']['IV Details']['Vendor Gstin State Name'] = $vendorGstinStateName;
            $auditTrail['action_data']['IV Details']['Vendor_Document_File'] = $vendorDocumentFile;
            $auditTrail['action_data']['IV Details']['GRN Created By'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['IV Details']['GRN Updated By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['IV Details']['GRN Approved Status'] = $grnApprovedStatus;


            // Insert all GRN items
            foreach ($grnItemList as $grnItem) {
                $oneItemId = $grnItem["itemId"];
                $oneItemCode = $grnItem["itemCode"];
                $oneItemHsn = $grnItem["itemHsn"];
                $oneItemName = $grnItem["itemName"];
                $oneItemQty = $grnItem["itemQty"];
                $oneItemTax = $grnItem["itemTax"];
                $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                $oneItemStocksQty = $grnItem["itemStockQty"];
                $oneItemReceivedQty = $grnItem["itemReceivedQty"];
                ///////////////////////////////////////////////////////////
                $oneItemCGST = $grnItem["itemCGST"] ?? "0.0";
                $oneItemSGST = $grnItem["itemSGST"] ?? "0.0";
                $oneItemIGST = $grnItem["itemIGST"] ?? "0.0";
                $oneItemTDS = $grnItem["itemTDS"] ?? "";
                $itemUom = $grnItem["itemUOM"] ?? "";
                $taxComponents2 = json_encode($grnItem['itemtax']) ?? "";
                $oneItemInsertQuery = 'INSERT INTO `erp_grninvoice_goods` 
                                        SET `grnId`="' . $id . '",
                                            `grnCode`="' . $grnCode . '",
                                            `goodName`="' . $oneItemName . '",
                                            `goodDesc`="",
                                            `grnIvCode`="' . $grnIvCode . '",
                                            `grnIvId`="' . $grnIVId . '",
                                            `goodId`="' . $oneItemId . '",
                                            `tds`="' . $oneItemTDS . '",
                                            `grnType`="' . $grnType . '",
                                            `goodCode`="' . $oneItemCode . '",
                                            `goodHsn`="' . $oneItemHsn . '",
                                            `goodQty`="' . $oneItemQty . '",
                                            `receivedQty`="' . $oneItemReceivedQty . '",
                                            `unitPrice`="' . $oneItemUnitPrice . '",
                                            `cgst`="' . $oneItemCGST . '", 
                                            `sgst`="' . $oneItemSGST . '",
                                            `igst`="' . $oneItemIGST . '", 
                                            `taxComponents`=' . $taxComponents2 . ',
                                            `totalAmount`="' . $oneItemTotalPrice . '",  
                                            `itemStocksQty`="' . $oneItemStocksQty . '",
                                            `itemUOM`="' . $itemUom . '", 
                                            `itemStorageLocation`=' . $oneItemStorageLocationId . ', 
                                            `grnGoodCreatedBy`="' . $created_by . '",
                                            `grnGoodUpdatedBy`="' . $updated_by . '"';

                $oneItemInsertObj = queryInsert($oneItemInsertQuery);
                if ($oneItemInsertObj["status"] != "success") {
                    $errorsInGrnItemsAdd++;
                } else {

                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN_Code'] = $grnCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Name'] = $oneItemName;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Desc'] = "";
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Iv Code'] = $grnIvCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['TDS'] = decimalValuePreview($oneItemTDS);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN_Type'] = $grnType;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Code'] = $oneItemCode;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_HSN'] = $oneItemHsn;
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Good_Qty'] = decimalQuantityPreview($oneItemQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Received_Qty'] = decimalQuantityPreview($oneItemReceivedQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Unit_Price'] = decimalValuePreview($oneItemUnitPrice);

                    if($companyCountry == 103) {
                        $auditTrail['action_data']['Item Details'][$oneItemCode]['CGST'] = decimalValuePreview($oneItemCGST);
                        $auditTrail['action_data']['Item Details'][$oneItemCode]['SGST'] = decimalValuePreview($oneItemSGST);
                        $auditTrail['action_data']['Item Details'][$oneItemCode]['IGST'] = decimalValuePreview($oneItemIGST);
                    } else {
                        $decodedTaxComponents2 = json_decode($taxComponents2, true);
                    
                        if (!empty($decodedTaxComponents2)) {
                            foreach ($decodedTaxComponents2 as $component) {
                                $gstType = $component['gstType'] ?? '';
                                $taxAmount = $component['taxAmount'] ?? 0;
                    
                                if ($gstType !== '') {
                                    $auditTrail['action_data']['Item Details'][$oneItemCode][strtoupper($gstType)] = decimalValuePreview($taxAmount);
                                }
                            }
                        }
                    }

                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Total_Amount'] = decimalValuePreview($oneItemTotalPrice);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['Item Stocks Qty'] = decimalQuantityPreview($oneItemStocksQty);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Good Created By'] = getCreatedByUser($created_by);
                    $auditTrail['action_data']['Item Details'][$oneItemCode]['GRN Good Updated By'] = getCreatedByUser($updated_by);
                }
            }

            $updatemultiple_grn = queryUpdate("UPDATE `erp_grn` SET `iv_status`= '1' WHERE `grnId`='" . $id . "'");

            $auditTrailreturn = generateAuditTrail($auditTrail);

            return [
                "status" => "success",
                "message" => "Invoice posted successfully." . $grnIvCode,
                "grnIVId" => $grnIVId,
                "data" => $oneItemInsertObj
            ];

            // return [
            //     "status"=> "warning",
            //     "message"=> "GRN posted failed, try again! ".$errorsInGrnItemsAdd.",".$errorsItemStockSummaryUpdate.",".$errorsItemStockLogInsert
            // ];
        }
    }





    function createGrnAsDraft($INPUTS)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $returnData = [];
        //return $INPUTS;
        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "vendorGstin" => "required",
            "totalInvoiceCGST" => "required",
            "totalInvoiceSGST" => "required",
            "totalInvoiceIGST" => "required",
            "totalInvoiceSubTotal" => "required",
            "totalInvoiceTotal" => "required",
            "grnItemList" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $grnPoNumber = $INPUTS["invoicePoNumber"];
        $grnCode = $INPUTS["grnCode"];
        $documentNo = $INPUTS["documentNo"];
        $documentDate = $INPUTS["documentDate"];
        $invoicePostingDate = $INPUTS["invoicePostingDate"];
        $invoiceDueDate = $INPUTS["invoiceDueDate"];
        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        $vendorId = $INPUTS["vendorId"];
        $vendorCode = $INPUTS["vendorCode"];
        $vendorName = $INPUTS["vendorName"];
        $vendorGstin = $INPUTS["vendorGstin"];
        $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
        $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
        $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
        $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
        $totalInvoiceTotal = $INPUTS["totalInvoiceTotal"];
        $locationGstinStateName = $INPUTS["locationGstinStateName"];
        $vendorGstinStateName = $INPUTS["vendorGstinStateName"];
        $vendorDocumentFile = $INPUTS["vendorDocumentFile"];
        $grnItemList = $INPUTS["grnItemList"];

        $grnApprovedStatus = "approved";

        foreach ($grnItemList as $itemKey => $grnItem) {
            if ($grnItem["itemQty"] != $grnItem["itemReceivedQty"]) {
                $grnApprovedStatus = "pending";
                break;
            } else {
                $isCompanyPoEnabled = $this->getCompanyDetails()["isPoEnabled"] ?? "";
                if ($isCompanyPoEnabled) {
                    if ($grnPoNumber == "") {
                        $grnApprovedStatus = "pending";
                        break;
                    }
                }
            }
            $grnItemList[$itemKey]["parentGlId"] = $this->getInventoryItemParentGl($grnItem["itemId"]);
        }

        $inserGrnObj = queryInsert('INSERT INTO `erp_grn_draft` SET 
                            `companyId`="' . $company_id . '",
                            `branchId`="' . $branch_id . '",
                            `locationId`="' . $location_id . '",
                            `functionalAreaId`="",
                            `grnCode`="' . $grnCode . '",
                            `grnPoNumber`="' . $grnPoNumber . '",
                            `vendorId`=' . $vendorId . ',
                            `vendorCode`="' . $vendorCode . '",
                            `vendorGstin`="' . $vendorGstin . '",
                            `vendorName`="' . $vendorName . '",
                            `vendorDocumentNo`="' . $documentNo . '",
                            `vendorDocumentDate`="' . $documentDate . '",
                            `postingDate`="' . $invoicePostingDate . '",
                            `dueDate`="' . $invoiceDueDate . '",
                            `dueDays`="' . $invoiceDueDays . '",
                            `grnSubTotal`="' . $totalInvoiceSubTotal . '",
                            `grnTotalCgst`="' . $totalInvoiceCGST . '",
                            `grnTotalSgst`="' . $totalInvoiceSGST . '",
                            `grnTotalIgst`="' . $totalInvoiceIGST . '",
                            `grnTotalAmount`="' . $totalInvoiceTotal . '",
                            `locationGstinStateName`="' . $locationGstinStateName . '",
                            `vendorGstinStateName`="' . $vendorGstinStateName . '",
                            `vendorDocumentFile`="' . $vendorDocumentFile . '",
                            `grnCreatedBy`="' . $created_by . '",
                            `grnUpdatedBy`="' . $updated_by . '",
                            `grnApprovedStatus`="' . $grnApprovedStatus . '"');


        if ($inserGrnObj["status"] != "success") {
            return $inserGrnObj;
        } else {
            $grnId = $inserGrnObj["insertedId"];
            $errorsInGrnItemsAdd = 0;

            // Insert all GRN items
            foreach ($grnItemList as $grnItem) {
                $oneItemId = $grnItem["itemId"];
                $oneItemCode = $grnItem["itemCode"];
                $oneItemHsn = $grnItem["itemHsn"];
                $oneItemName = $grnItem["itemName"];
                $oneItemQty = $grnItem["itemQty"];
                $oneItemTax = $grnItem["itemTax"];
                $oneItemUnitPrice = $grnItem["itemUnitPrice"];
                $oneItemTotalPrice = $grnItem["itemTotalPrice"];
                $oneItemStorageLocationId = $grnItem["itemStorageLocationId"];
                $oneItemStocksQty = $grnItem["itemStockQty"];
                $oneItemReceivedQty = $grnItem["itemReceivedQty"];

                $oneItemInsertQuery = 'INSERT INTO `erp_grn_goods_draft` SET `grnId`="' . $grnId . '",`grnCode`="' . $grnCode . '",`goodName`="' . $oneItemName . '",`goodDesc`="",`goodId`="' . $oneItemId . '",`goodCode`="' . $oneItemCode . '",`goodHsn`="' . $oneItemHsn . '",`goodQty`="' . $oneItemQty . '",`receivedQty`="' . $oneItemReceivedQty . '",`unitPrice`="' . $oneItemUnitPrice . '",`totalAmount`="' . $oneItemTotalPrice . '",  `itemStocksQty`="' . $oneItemStocksQty . '", `itemStorageLocation`=' . $oneItemStorageLocationId . ', `grnGoodCreatedBy`="' . $created_by . '",`grnGoodUpdatedBy`="' . $updated_by . '"';

                $oneItemInsertObj = queryInsert($oneItemInsertQuery);
                if ($oneItemInsertObj["status"] != "success") {
                    $errorsInGrnItemsAdd++;
                }
            }

            if ($errorsInGrnItemsAdd == 0) {
                return [
                    "status" => "success",
                    "message" => "GRN posted successfully as draft"
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "GRN posted failed, try again!"
                ];
            }
        }
    }


    function getGrnDetails($grnId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        if ($grnId == null) {
            return [
                "status" => "warning",
                "message" => "Invalid GRN id specified",
                "data" => []
            ];
        }
        return queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE (`grnId`=' . $grnId . ' AND `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ')');
    }

    function getGrnItemDetails($grnId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        if ($grnId == null) {
            return [
                "status" => "warning",
                "message" => "Invalid GRN id specified",
                "data" => []
            ];
        }
        //return queryGet('SELECT * FROM `'.ERP_GRN_GOODS.'` WHERE `grnId`='.$grnId, true);
        return queryGet('SELECT grnGoods.*, storageLocation.`storage_location_code`,storageLocation.`storage_location_name` FROM `erp_grn_goods` as grnGoods,`erp_storage_location` as storageLocation WHERE grnGoods.`itemStorageLocation`=storageLocation.`storage_location_id` AND grnGoods.`grnId`=' . $grnId, true);
    }

    function getSrnItemDetails($grnId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        if ($grnId == null) {
            return [
                "status" => "warning",
                "message" => "Invalid GRN id specified",
                "data" => []
            ];
        }
        //return queryGet('SELECT * FROM `'.ERP_GRN_GOODS.'` WHERE `grnId`='.$grnId, true);
        // return queryGet('SELECT grnGoods.*, costCenter.`CostCenter_code`,costCenter.`CostCenter_desc` FROM `erp_grn_goods` as grnGoods,`erp_cost_center` as costCenter WHERE grnGoods.`itemStorageLocation`=costCenter.`CostCenter_id` AND grnGoods.`grnId`=' . $grnId, true);
        return queryGet("SELECT 
        grnGoods.*, 
        CASE 
            WHEN grnGoods.itemStorageLocation = 0 THEN 'Inventorize'
            ELSE costCenter.CostCenter_code
        END AS CostCenter_code,
        CASE 
            WHEN grnGoods.itemStorageLocation = 0 THEN 'Inventorize'
            ELSE costCenter.CostCenter_desc
        END AS CostCenter_desc 
    FROM 
        erp_grn_goods AS grnGoods
    LEFT JOIN 
        (SELECT CostCenter_id, CostCenter_code, CostCenter_desc FROM erp_cost_center WHERE CostCenter_id <> 0) AS costCenter 
        ON grnGoods.itemStorageLocation = costCenter.CostCenter_id
    WHERE 
        grnGoods.grnId = " . $grnId, true);
    }


    //***************************Imran Vendor Payment Start******************************/

    function fetchAllVendor()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $ins = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '$company_id' AND (`vendor_status` != 'deleted' OR `vendor_status` = 'guest')";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function fetchVendorDetails($id)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;

        $ins = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '$company_id' AND `vendor_id`='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }


    // insert collect payment
    function insertVendorPayment($POST, $FILES)
    {
        // console($_POST);
        // exit;
        $returnData = [];
        $returndata = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        // console($POST);
        // exit;
        $collectPayment = $POST['paymentDetails']['collectPayment'];
        $totalDueAmt = $POST['paymentDetails']['totalDueAmt'];
        $totalInvAmt = $POST['paymentDetails']['totalInvAmt'];
        $remaningAmt = $POST['paymentDetails']['remaningAmt'];
        $bankId = $POST['paymentDetails']['bankId'] ?? 0;
        $bankDetails = get_acc_bank_cash_accounts_details($bankId);
        $perentglid = $bankDetails['parent_gl'];
        $POST['paymentDetails']['bank'] = $bankDetails;
        $POST['paymentDetails']['accCode'] = $bankDetails['acc_code'];
        $POST['paymentDetails']['accName'] = $bankDetails['bank_name'];
        $advancedPayAmt = $POST['paymentDetails']['advancedPayAmt'];
        $paymentCollectType = '';
        if ($POST['paymentDetails']['paymentCollectType'] == 'collect') {
            $paymentCollectType = $POST['paymentDetails']['paymentCollectType'];
        } elseif ($POST['paymentDetails']['paymentAdjustType'] == 'adjust') {
            $paymentCollectType = $POST['paymentDetails']['paymentAdjustType'];
        }
        $postingDate = $POST['paymentDetails']['postingDate'];
        $documentDate = $POST['paymentDetails']['documentDate'];
        $tnxDocNo = $POST['paymentDetails']['tnxDocNo'];
        $type = $POST['type'] ?? "vendor";
        $roundOffValue = 0;
        $vendorId = 0;
        $customer_id = 0;
        if ($type == 'vendor') {
            $vendorId = $POST['paymentDetails']['vendorId'];
        } else {
            $customer_id = $POST['paymentDetails']['vendorId'] ?? 0;
        }


        $paymentCode = date('dmY') . rand(1111, 9999) . rand(1111, 9999);
        $paymentAdviceImg = date('dmY') . rand(1111, 9999) . '_' . $POST['paymentDetails']['paymentAdviceImg'];
        // $paymentAdviceImg=uploadFile($d, "../../public/storage/invoices/payment-advice/",["jpg","png","ico","jpeg"]);
        // if($logoObj["status"]=="success"){
        // console('payment advice image******************', $paymentAdviceImg);
        // console($paymentAdviceImg);
        // console('payment advice image******************', $paymentAdviceImg);
        if ($perentglid == 91) {
            $balance = $this->getbalance($perentglid, $postingDate);
            if ($balance < $collectPayment) {
                $returnData['status'] = "warning";
                $returnData['message'] = "Payment failed for insufficient balance";
                return $returnData;
            }
        }
        $insPayment = "INSERT INTO `" . ERP_GRN_PAYMENTS . "` 
                SET
                    `paymentCode`='$paymentCode',
                    `vendor_id`='$vendorId',
                    `customer_id`='$customer_id',
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `collect_payment`='$collectPayment',
                    `bank_id`='$bankId',
                    `payment_advice`='$paymentAdviceImg',
                    `paymentCollectType`='$paymentCollectType',
                    `documentDate`='$documentDate',
                    `transactionId`='$tnxDocNo',
                    `postingDate`='$postingDate',
                    `remarks`='$vendorId',
                    `type`='$type',
                    `created_by`='$created_by',
                    `updated_by`='$updated_by'
                    ";

        $insCollectObj = queryInsert($insPayment);
        if ($insCollectObj['status'] = 'success') {
            $paymentId = $insCollectObj['insertedId'];


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_GRN_PAYMENTS;
            $auditTrail['basicDetail']['column_name'] = 'payment_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $paymentId;  // primary key
            $auditTrail['basicDetail']['party_type'] = $type;
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $paymentCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = $paymentCollectType;  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insPayment);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Collection Details']['transactionId'] = $tnxDocNo;
            $auditTrail['action_data']['Collection Details']['amount'] = $collectPayment;
            $auditTrail['action_data']['Collection Details']['Account_Detail'] = $bankDetails['bank_name'] . ' (' . $bankDetails['acc_code'] . ')';
            $auditTrail['action_data']['Collection Details']['paymentCollectType'] = $paymentCollectType;
            $auditTrail['action_data']['Collection Details']['documentDate'] = formatDateORDateTime($documentDate);
            $auditTrail['action_data']['Collection Details']['postingDate'] = formatDateORDateTime($postingDate);
            $auditTrail['action_data']['Collection Details']['remarks'] = 'Payment';
            $auditTrail['action_data']['Collection Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Collection Details']['updated_by'] = getCreatedByUser($updated_by);

            $paymentInvItems = $POST['paymentInvoiceDetails'];

            $paymentInvItems = array_filter($paymentInvItems, function ($item) {
                return $item['recAmt'] !== '' && $item['recAmt'] !== 0;
            });

            //All Details of payments
            $remaining_amt = $remaningAmt ?? 0;
            $enter_amt = $collectPayment ?? 0;
            $adv_amt = $advancedPayAmt;
            if (!isset($adv_amt) || $adv_amt == "") {
                $adv_amt = 0;
            }

            $total_amt = 0;
            $invoiceConcadinate = '';
            foreach ($paymentInvItems as $one) {
                $grnId = $one['grnIvId'] ?? 0;
                $invAmt = $one['invAmt'];
                $recAmt = $one['recAmt'];
                $dueAmt = $one['dueAmt'];
                // $calDueAmt = $dueAmt - $recAmt;
                if (isset($recAmt) && $recAmt != null) {
                    $invoiceConcadinate .= $one['grnCode'] . '| ';
                    $total_amt += $recAmt;
                    $calPartialPaidAmt = ($dueAmt - $recAmt);
                    $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                    SET
                                        `payment_id`='$paymentId',
                                        `company_id`='$company_id',
                                        `branch_id`='$branch_id',
                                        `location_id`='$location_id',
                                        `vendor_id`='$vendorId',
                                        `customer_id`='$customer_id',
                                        `grn_id`='$grnId',
                                        `payment_type`='pay',
                                        `payment_amt`='$recAmt',
                                        `remarks`='$grnId',
                                        `created_by`='$created_by',
                                        `updated_by`='$updated_by'";

                    if ($dbCon->query($insItem)) {
                        if ($recAmt < $dueAmt) {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                        SET
                                            `paymentStatus`='2',
                                            `dueAmt`='$calPartialPaidAmt' WHERE `grnIvId`='$grnId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);
                        } else if ($recAmt == $dueAmt) {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                        SET
                                            `paymentStatus`='4',
                                            `dueAmt`='0' WHERE `grnIvId`='$grnId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);
                        } else {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                        SET
                                            `paymentStatus`='1',
                                            `dueAmt`='5' WHERE `grnIvId`='$grnId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);


                            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['invoiceNo'] = $one['grnCode'];
                            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['type'] = 'pay';
                            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['amount'] = $recAmt;
                            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['remarks'] = 'Pay For' . $one['grnCode'];
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Somthing went wrong 11";
                    }
                } else {
                    continue;
                }
            }


            //-----------------------------Payment ACC Start----------------
            $invoiceConcadinate = $invoiceConcadinate != '' ? $invoiceConcadinate : 'Advance payment';
            $paymentInputData = [
                "BasicDetails" => [
                    "documentNo" => $tnxDocNo, // Invoice Doc Number
                    "documentDate" => $documentDate, // Invoice number
                    "postingDate" =>  $postingDate, // current date
                    "reference" => $tnxDocNo, // T code
                    "remarks" => "Payment for - " . $invoiceConcadinate,
                    "journalEntryReference" => "Payment/Expenses"
                ],
                "paymentDetails" => $POST['paymentDetails'],
                "vendorDetails" => ($type != 'customer') ? $this->fetchVendorDetails($vendorId)['data'][0] : $this->fetchCustomerDetails($customer_id)['data'][0],
                "paymentInvItems" => $paymentInvItems,
                "roundOffValue" => $roundOffValue,
                "type" => $type
            ];
            $paymentObj = $this->paymentAccountingPosting($paymentInputData, "Payment", $paymentId);
            if ($paymentObj['status'] == 'success') {
                $JournalId = $paymentObj['journalId'];
                $sqlpayment = "UPDATE `" . ERP_GRN_PAYMENTS . "`
                            SET
                                `journal_id`=$JournalId 
                            WHERE `payment_id`='$paymentId'  ";
                queryUpdate($sqlpayment);
            }
            //-----------------------------Payment ACC END ----------------

            $returndata['paymentid'] = $paymentId;
            $returndata['status'] = "success";
            $returndata['message'] = "Inserted Successfull";
            $returndata['paymentInputData'] = $paymentInputData;
            $returndata['paymentObj'] = $paymentObj;
            $returndata['balance'] = $balance;
            $returndata['collectPayment'] = $collectPayment;
            
            


            // console("print total amt ******************");
            // console($total_amt);
            // console("print total amt ******************");
            if ($adv_amt > 0) {
                if ($enter_amt > 0) {
                    if ($total_amt < $adv_amt) {
                        $total_amt = $total_amt * -1;
                        $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                        SET
                                            `payment_id`='$paymentId',
                                            `company_id`='$company_id',
                                            `branch_id`='$branch_id',
                                            `location_id`='$location_id',
                                            `vendor_id`='$vendorId',
                                            `customer_id`='$customer_id',
                                            `grn_id`='0',
                                            `payment_type`='advanced',
                                            `payment_amt`='$total_amt',
                                            `remarks`='',
                                            `created_by`='$created_by',
                                            `updated_by`='$updated_by'
                                            ";
                        $dbCon->query($insItem);

                        $insItem2 = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                            SET
                                `payment_id`='$paymentId',
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `vendor_id`='$vendorId',
                                `customer_id`='$customer_id',
                                `grn_id`='0',
                                `payment_type`='advanced',
                                `payment_amt`='$enter_amt',
                                `remarks`='',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                        $dbCon->query($insItem2);
                    } elseif ($total_amt >= $adv_amt) {
                        $adv_amt = $adv_amt * -1;
                        $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                            SET
                                `payment_id`='$paymentId',
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `vendor_id`='$vendorId',
                                `customer_id`='$customer_id',
                                `grn_id`='0',
                                `payment_type`='advanced',
                                `payment_amt`='$adv_amt',
                                `remarks`='',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                        $dbCon->query($insItem);

                        $remaining = $enter_amt - $total_amt;
                        $insItem2 = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                        SET
                                            `payment_id`='$paymentId',
                                            `company_id`='$company_id',
                                            `branch_id`='$branch_id',
                                            `location_id`='$location_id',
                                            `vendor_id`='$vendorId',
                                            `customer_id`='$customer_id',
                                            `grn_id`='0',
                                            `payment_type`='advanced',
                                            `payment_amt`='$remaining',
                                            `remarks`='',
                                            `created_by`='$created_by',
                                            `updated_by`='$updated_by'
                                        ";
                        $dbCon->query($insItem2);
                    }
                } else {
                    $total_amt = $total_amt * -1;
                    $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                    SET
                                        `payment_id`='$paymentId',
                                        `company_id`='$company_id',
                                        `branch_id`='$branch_id',
                                        `location_id`='$location_id',
                                        `vendor_id`='$vendorId',
                                        `customer_id`='$customer_id',
                                        `grn_id`='0',
                                        `payment_type`='advanced',
                                        `payment_amt`='$total_amt',
                                        `remarks`='',
                                        `created_by`='$created_by',
                                        `updated_by`='$updated_by'
                                        ";
                    $dbCon->query($insItem);
                }
            } else {
                $total_amt = $total_amt * -1;
                $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                SET
                                    `payment_id`='$paymentId',
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `vendor_id`='$vendorId',
                                    `customer_id`='$customer_id',
                                    `grn_id`='0',
                                    `payment_type`='advanced',
                                    `payment_amt`='$total_amt',
                                    `remarks`='',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by'
                                    ";
                $dbCon->query($insItem);

                $insItem2 = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                        SET
                            `payment_id`='$paymentId',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `vendor_id`='$vendorId',
                            `customer_id`='$customer_id',
                            `grn_id`='0',
                            `payment_type`='advanced',
                            `payment_amt`='$enter_amt',
                            `remarks`='',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'
                            ";
                $dbCon->query($insItem2);
            }

            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['invoiceNo'] = 'NULL';
            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['type'] = 'pay';
            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['amount'] = $adv_amt;
            $auditTrail['action_data']['Payment Log Details'][$paymentCode]['remarks'] = 'Advance Pay';

            $auditTrailreturn = generateAuditTrail($auditTrail);
        } else {
            $returndata['status'] = "warning";
            $returndata['message'] = "Somthing went wrong 22";
        }
        return $returndata;
    }

    // multi-vendor payment 
    function insertMultiVendorPayment($POST)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $companyNameNav;
        // console($POST);
        // exit;
        $collectPayment = 0;
        $currency_id = '';
        $totalDueAmt = $POST['paymentDetails']['totalDueAmt'];
        $totalInvAmt = $POST['paymentDetails']['totalInvAmt'];
        $remaningAmt = $POST['paymentDetails']['remaningAmt'];
        $collectPayment = $POST['paymentDetails']['collectPayment'];
        $bankId = $POST['paymentDetails']['bankId'] ?? 0;
        $vendorDetails = $POST['vendorDetails'] ?? "";
        $bankDetails = get_acc_bank_cash_accounts_details($bankId);
        $POST['paymentDetails']['bank'] = $bankDetails;
        $POST['paymentDetails']['accCode'] = $bankDetails['acc_code'];
        $POST['paymentDetails']['accName'] = $bankDetails['bank_name'];
        $perentglid = $bankDetails['parent_gl'];
        $advancedPayAmt = $POST['paymentDetails']['advancedPayAmt'];
        $paymentCollectType = '';
        $paymentCollectTypeNew = '';
        if ($POST['paymentDetails']['paymentCollectType'] == 'collect') {
            $paymentCollectType = $POST['paymentDetails']['paymentCollectType'];
            $paymentCollectTypeNew = 'Payment';
        } elseif ($POST['paymentDetails']['paymentAdjustType'] == 'adjust') {
            $paymentCollectType = $POST['paymentDetails']['paymentAdjustType'];
            $paymentCollectTypeNew = 'Adjustment';
        }
        $postingDate = $POST['paymentDetails']['postingDate'];
        $documentDate = $POST['paymentDetails']['documentDate'];
        $tnxDocNo = $POST['paymentDetails']['tnxDocNo'];

        $remarks = addslashes($POST['paymentDetails']['remarks']);
        $balance = $this->getbalance($perentglid, $postingDate);
        $paymentAdviceImg = date('dmY') . rand(1111, 9999) . '_' . $POST['paymentDetails']['paymentAdviceImg'];
        // $paymentAdviceImg=uploadFile($d, "../../public/storage/invoices/payment-advice/",["jpg","png","ico","jpeg"]);
        // if($logoObj["status"]=="success"){
        // console('payment advice image******************', $paymentAdviceImg);
        // console($paymentAdviceImg);
        // console('payment advice image******************', $paymentAdviceImg);

        $accPaymentInvItems = [];
        $paymentInvItems = $POST['paymentInvoiceDetails'];
        $paymentInvItems = array_filter($paymentInvItems, function ($item) {
            return $item['recAmt'] !== '' && $item['recAmt'] !== 0;
        });
        if ($perentglid == 91) {
            $balance = $this->getbalance($perentglid, $postingDate);
            if ($balance < $collectPayment) {
                $returnData['status'] = "warning";
                $returnData['message'] = "Payment failed for insufficient balance";
                return $returnData;
            }
        }

        $payment_error = 0;
        $payment_log_error = 0;
        foreach ($paymentInvItems as $vendor_id => $payment) {

            $paymentCode = date('dmY') . rand(1111, 9999) . rand(1111, 9999);
            //Finding Collect Payment
            $collectPayment = array_sum(array_column($payment, 'paymentINR'));

            $insPayment = "INSERT INTO `" . ERP_GRN_PAYMENTS . "` 
            SET
                `paymentCode`='$paymentCode',
                `vendor_id`='$vendor_id',
                `company_id`='$company_id',
                `branch_id`='$branch_id',
                `location_id`='$location_id',
                `collect_payment`='$collectPayment',
                `bank_id`='$bankId',
                `payment_advice`='$paymentAdviceImg',
                `paymentCollectType`='$paymentCollectType',
                `documentDate`='$documentDate',
                `transactionId`='$tnxDocNo',
                `postingDate`='$postingDate',
                `type`='vendor',
                `remarks`='$remarks',
                `created_by`='$created_by',
                `updated_by`='$updated_by'
                ";
            $insCollectObj = queryInsert($insPayment);

            if ($insCollectObj['status'] = 'success') {
                $paymentId = $insCollectObj['insertedId'];

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_GRN_PAYMENTS;
                $auditTrail['basicDetail']['column_name'] = 'payment_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $paymentId;  // primary key
                $auditTrail['basicDetail']['party_type'] = 'vendor';
                $auditTrail['basicDetail']['party_id'] = $vendor_id;
                $auditTrail['basicDetail']['document_number'] = $paymentCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = $paymentCollectTypeNew;  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insPayment);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = $remarks;

                $auditTrail['action_data']['Collection Details']['transactionId'] = $tnxDocNo;
                $auditTrail['action_data']['Collection Details']['amount'] = $collectPayment;
                $auditTrail['action_data']['Collection Details']['Account_Detail'] = $bankDetails['bank_name'] . ' (' . $bankDetails['acc_code'] . ')';
                $auditTrail['action_data']['Collection Details']['paymentCollectType'] = $paymentCollectType;
                $auditTrail['action_data']['Collection Details']['documentDate'] = formatDateORDateTime($documentDate);
                $auditTrail['action_data']['Collection Details']['postingDate'] = formatDateORDateTime($postingDate);
                $auditTrail['action_data']['Collection Details']['remarks'] = 'Payment';
                $auditTrail['action_data']['Collection Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Collection Details']['updated_by'] = getCreatedByUser($updated_by);

                $total_amt = 0;
                $invoiceConcadinate = '';

                //Fetch Data from Vendor Table By vendor ID
                $sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id = $vendor_id");

                $vendor_code = $sql["data"]["vendor_code"];
                $vendor_Name = $sql["data"]["trade_name"];
                $vendorParentGl = $sql["data"]["parentGlId"];
                $accPaymentInvItems[$vendor_id]['vendor_id'] = $vendor_id;
                $accPaymentInvItems[$vendor_id]['vendorParentGl'] = $vendorParentGl;
                $accPaymentInvItems[$vendor_id]['vendor_code'] = $vendor_code;
                $accPaymentInvItems[$vendor_id]['vendor_name'] = $vendor_Name;
                $accPaymentInvItems[$vendor_id]['paymentCode'] = $paymentCode;
                $accPaymentInvItems[$vendor_id]['paymentId'] = $paymentId;
                $accPaymentInvItems[$vendor_id]['bankId'] = $bankId;

                foreach ($payment as $key => $eachInv) {
                    $grnId = $eachInv['grnIvId'] ?? 0;
                    $invAmt = $eachInv['invAmt'];
                    $recAmt = $eachInv['recAmt'];
                    $recAmtInr = $eachInv['paymentINR'];
                    $dueAmt = $eachInv['dueAmt'];
                    $adjustedAMT = $eachInv['paymentAdjustINR'];
                    $roundoff = $eachInv['inputRoundOffInrWithSign'];
                    $writeback = $eachInv['inputWriteBackInrWithSign'];
                    $financial_charge = $eachInv['inputFinancialChargesInrWithSign'];
                    $forex = $eachInv['inputForexLossGainInrWithSign'];

                    $currency_id = $eachInv['currency_id'];
                    $currency_rate = $eachInv['currencyRate'];
                    // $newVendorId = $eachInv['vendorId'];
                    // $calDueAmt = $dueAmt - $recAmt;
                    if (isset($recAmtInr) && $recAmtInr != null) {
                        $invoiceConcadinate .= $eachInv['grnCode'] . '| ';
                        $total_amt += $recAmtInr;
                        $calPartialPaidAmt = ($dueAmt - $recAmtInr + $adjustedAMT);
                        $insItem = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                                            SET
                                                `payment_id`='$paymentId',
                                                `company_id`='$company_id',
                                                `branch_id`='$branch_id',
                                                `location_id`='$location_id',
                                                `vendor_id`='" . $vendor_id . "',
                                                `grn_id`='$grnId',
                                                `payment_type`='pay',
                                                `payment_amt`='$recAmtInr',
                                                `roundoff`='$roundoff',
                                                `writeback`='$writeback',
                                                `financial_charge`='$financial_charge',
                                                `forex`='$forex',
                                                `currency_id`='$currency_id',
                                                `currency_rate`='$currency_rate',
                                                `remarks`='$remarks',
                                                `created_by`='$created_by',
                                                `updated_by`='$updated_by'";

                        if ($dbCon->query($insItem)) {
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['grnId'] = $grnId;
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['grnCode'] = $eachInv['grnCode'];
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['roundoff'] = $roundoff;
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['writeback'] = $writeback;
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['financial_charge'] = $financial_charge;
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['forex'] = $forex;
                            $accPaymentInvItems[$vendor_id]['paymentItems'][$key]['recAmt'] = $recAmt;
                            if ($recAmtInr < ($dueAmt + $adjustedAMT)) {
                                // update invoice items
                                $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                                    SET
                                                        `paymentStatus`='2',
                                                        `dueAmt`='$calPartialPaidAmt' WHERE `grnIvId`='$grnId'";
                                $dbCon->query($upd);
                                // console($returnData['ss'] = $upd);
                            } else if ($recAmtInr == ($dueAmt + $adjustedAMT)) {
                                // update invoice items
                                $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                                    SET
                                                        `paymentStatus`='4',
                                                        `dueAmt`='0' WHERE `grnIvId`='$grnId'";
                                $dbCon->query($upd);
                                // console($returnData['ss'] = $upd);
                            } else {
                                // update invoice items
                                $upd = "UPDATE `" . ERP_GRNINVOICE . "` 
                                                    SET
                                                        `paymentStatus`='1',
                                                        `dueAmt`='5' WHERE `grnIvId`='$grnId'";
                                $dbCon->query($upd);
                                // console($returnData['ss'] = $upd);


                                $auditTrail['action_data']['Payment Log Details'][$paymentCode]['invoiceNo'] = $eachInv['grnCode'];
                                $auditTrail['action_data']['Payment Log Details'][$paymentCode]['type'] = 'pay';
                                $auditTrail['action_data']['Payment Log Details'][$paymentCode]['amount'] = $recAmtInr;
                                $auditTrail['action_data']['Payment Log Details'][$paymentCode]['remarks'] = 'Pay For' . $eachInv['grnCode'];
                            }
                        } else {
                            // $returnData['status'] = "warning";
                            // $returnData['message'] = "Somthing went wrong 11";
                            $payment_log_error++;
                        }
                    } else {
                        continue;
                    }
                }
            } else {
                $payment_error++;
            }
        }
        $auditTrailreturn = generateAuditTrail($auditTrail);
        //-----------------------------Payment ACC Start----------------
        $invoiceConcadinate = $invoiceConcadinate != '' ? $invoiceConcadinate : 'Advance payment';
        $paymentInputData = [
            "BasicDetails" => [
                "documentNo" => $tnxDocNo, // Invoice Doc Number
                "documentDate" => $documentDate, // Invoice number
                "postingDate" =>  $postingDate, // current date
                "reference" => $tnxDocNo, // T code
                "remarks" => "Payment for - " . $invoiceConcadinate . $remarks,
                "journalEntryReference" => "Payment/Expenses"
            ],
            "paymentDetails" => $accPaymentInvItems
        ];
        $paymentObj = $this->multipaymentAccountingPosting($paymentInputData, "Payment", $paymentId);
        if ($paymentObj['status'] == 'success') {
            foreach ($vendorDetails as $id => $vendor_id) {
                $currencyName = getSingleCurrencyType($currency_id);
                $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` = " . $id . "");
                $vendor = $vendor_sql['data'];
                $vendorName = $vendor['trade_name'];
                $vendor_authorised_person_email = $vendor['vendor_authorised_person_email'];
                $vendor_authorised_person_phone = $vendor['vendor_authorised_person_phone'];

                $vendor_code = $vendor['vendor_code'];
                $mailValid = $vendor['isMailValid'];
                if ($mailValid == 'yes') {
                    $returnData['vendor'] = $vendor;
                    $sub = "Payment Successfully Aproved";
                    $msg = "Dear $vendorName ,<br>			
                        We are writing to confirm the successful payment of your outstanding invoices and the invoice details are : <br>";

                    foreach ($paymentInvItems as $vendor_id => $payment) {
                        foreach ($payment as $key => $invoice) {
                            $grnCode = $invoice['grnCode'];
                            $invAmt = $invoice['recAmt'];
                            $date = $POST['paymentDetails']['postingDate'];
                            $msg .= "vendor invoice number: $grnCode , Pay Amount : $invAmt $currencyName <br> ";
                        }
                    }
                    $msg .= "Best regards, $companyNameNav";

                    $mail =  SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg, null, $vendor_code, 'ApprovedPayment', $paymentId, $tnxDocNo);
                }
                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'after_payment_to_vendor_against_invoice_msg';
                $whatsapparray['to'] = $vendor_authorised_person_phone;
                $whatsapparray['vendorname'] = $vendorName;
                $whatsapparray['current_userName'] = $current_userName;
                $whatsapparray['invoiceno'] = $tnxDocNo;

                SendMessageByWhatsappTemplate($whatsapparray);
            }
        }
        //-----------------------------Payment ACC END ----------------
        if ($payment_error == 0 && $payment_log_error == 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Inserted Successfull";
            $returnData['vendorDetails'] = $vendorDetails;
            $returnData['paymentInputData'] = $paymentInputData;
            $returnData['paymentObj'] = $paymentObj;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Payment failed, try again! " . $payment_error . "," . $payment_log_error;
        }
        $returnData['balance'] = $balance;
        return $returnData;
    }

    // fetch totalAdvanceAmt
    function fetchGrnAdvanceAmt($vendorId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT SUM(`payment_amt`) AS `totalAdvanceAmt` FROM `" . ERP_GRN_PAYMENTS_LOG . "` WHERE `vendor_id`='$vendorId' AND `payment_type`='advanced';";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchGRNByVendorId($vendorId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_GRN . "` WHERE vendorId='$vendorId' AND grnStatus!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchGRNInvoice()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv, `erp_grn` AS grn WHERE grniv.`companyId`='$company_id' AND grniv.`grnId` = grn.`grnId` AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`grnStatus`!='deleted' ORDER BY grniv.`grnIvId` DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchGRNInvoiceByVendorId($vendorId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_GRNINVOICE . "` WHERE vendorId='$vendorId' AND paymentStatus != 4 AND grnStatus = 'active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchGRNInvoiceById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_GRNINVOICE . "` WHERE grnIvId='$id' AND paymentStatus != 4 AND grnStatus!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchGRNById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_GRN . "` WHERE grnId='$id' AND grnStatus!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchAllGRN()
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_GRN . "` WHERE grnStatus!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchAllPayments()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM " . ERP_GRN_PAYMENTS . " WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `status`!='deleted' ORDER BY payment_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch GRN By VendorId imranali59059 20230112
    function fetchPaymentLogDetails($paymentId)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_GRN_PAYMENTS_LOG . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND payment_id='$paymentId' AND  status!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch totalAdvanceAmt
    function fetchAllPaymentLogByVendorId($vendorId)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT log.*,payment.documentDate,payment.transactionId FROM (SELECT payment_id, sum(payment_amt) as advancedAmt FROM `" . ERP_GRN_PAYMENTS_LOG . "` WHERE vendor_id='$vendorId' and payment_type = 'advanced' GROUP BY payment_id) as log INNER JOIN `" . ERP_GRN_PAYMENTS . "` as payment ON log.payment_id = payment.payment_id";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchCustomerDetails($id)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id='$company_id' AND company_branch_id='$branch_id' AND location_id='$location_id' AND `customer_id`='$id'";

        return queryGet($ins, true);
    }


    //***************************Imran Vendor Payment End***************************** */


}
