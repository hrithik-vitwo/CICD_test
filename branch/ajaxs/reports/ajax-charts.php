<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

//$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];

$payload = [];

//---------------------------------Start--------------------------------
//------------ Current date
$currentDate = date('Y-m-d');
//------------ 7 days ago
$sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

//----------------------quickDrop---------------------------
if(isset($_GET['quickDrop'])){
    $toDate  = $currentDate;
    $fromDate= date('Y-m-d', strtotime('-'.$_GET['quickDrop'].' days'));
}else{
    $toDate = $currentDate;
    $fromDate = $sevenDaysAgo;
}
//----------------------itemCode---------------------------
if(isset($_GET['itemCode'])){
    $itemCode = $_GET['itemCode'];
}else{
    $itemCode = '';
}
//----------------------profitCenter-----------------------
if(isset($_GET['profitCenter'])){
    $profitCenter = $_GET['profitCenter'];
}else{
    $profitCenter = '';
}
//----------------------specificDay------------------------
if (isset($_GET['specificDay'])) {
    $specificDay = $_GET['specificDay'];
}else{
    $specificDay = $currentDate;
}

//----------------------monthRange-------------------------
if (isset($_GET['monthRange'])) {
    $selectedMonth = $_GET['monthRange'];
    
    // Calculate the start and end dates of the selected month
    $monthFromRange = date('Y-m-01', strtotime("{$selectedMonth}-01"));
    $monthToRange = date('Y-m-t', strtotime("{$selectedMonth}-01"));
    
  }else{
    $monthFromRange = date('Y-m-01');
    $monthToRange = date('Y-m-t');
  }
//-------------------------------------------------------

//----------------------fiscal year-------------------------
if (isset($_GET['fYDropdown'])) {
    $year_id = $_GET['fYDropdown'];
    $year_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `year_variant_id`=$year_id");
    $data = $year_sql['data'];

    $start = explode('-', $data['year_start']);
    $end = explode('-', $data['year_end']);
    $startDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));
    $endDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));

    $toDate  = $startDate;
    $fromDate = $endDate;
}
//-------------------------------------------------------

if(!empty($_GET['search'])){
    updateInsertDashTableSettings($_GET);
}

//--------------------------End-----------------------------

// ADDING DATA TO PAYLOAD ON CONDITION
if($_GET['chart']=='dailyPeriodicSales'){
    $payload["dailyPeriodicSales"] = queryGet("SELECT invoice_date,SUM(sub_total_amt - totalDiscount) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active' GROUP BY invoice_date", true);

} else if($_GET['chart']=='monthlyPeriodicSales'){
    // $payload["monthlyPeriodicSales"] = queryGet("SELECT date_format(invoice_date,'%M') AS month,SUM(sub_total_amt - totalDiscount) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active' GROUP BY month", true);

} else if($_GET['chart']=='yearlyPeriodicSales'){
    // $payload["yearlyPeriodicSales"] = queryGet("SELECT SUM(sub_total_amt - totalDiscount) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active'", true);

} else if($_GET['chart']=='dailyProductQuantityWiseSales'){
    $payload["dailyProductQuantityWiseSales"] = queryGet("SELECT date_format(items.createdAt,'%Y-%m-%d') AS date_ , items.itemName AS item_name , SUM(qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND branch_id=$branch_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_,item_name ORDER BY date_", true);

} else if($_GET['chart']=='dailyProductPriceWiseSales'){
    $payload["dailyProductPriceWiseSales"] = queryGet("SELECT date_format(items.createdAt,'%Y-%m-%d') AS date_ , items.itemName AS item_name , SUM(items.basePrice - items.totalDiscountAmt) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_,item_name ORDER BY date_", true);

} else if($_GET['chart']=='monthlyProductQuantityWiseSales'){
    $payload["monthlyProductQuantityWiseSales"] = queryGet("SELECT  date_format(items.createdAt,'%M') AS month , items.itemName AS item_name , SUM(qty) AS total_qty  FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY month,item_name ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='monthlyProductPriceWiseSales'){
    $payload["monthlyProductPriceWiseSales"] = queryGet("SELECT date_format(items.createdAt,'%M') AS month , items.itemName AS item_name , SUM(items.basePrice - items.totalDiscountAmt) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY month,item_name ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='yearlyProductQuantityWiseSales'){
    $payload["yearlyProductQuantityWiseSales"] = queryGet("SELECT items.itemName AS item_name , SUM(items.qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='yearlyProductPriceWiseSales'){
    $payload["yearlyProductPriceWiseSales"] = queryGet("SELECT items.itemName AS item_name , SUM(items.basePrice - items.totalDiscountAmt) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY item_name Order By total_amount desc limit 10", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesOnDate'){
    $payload["productQuantityWiseSalesOnDate"] = queryGet("SELECT items.itemName AS item_name, SUM(items.qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id = items.so_invoice_id WHERE invoices.company_id = $company_id AND invoices.branch_id = $branch_id AND invoices.invoice_date = '".$specificDay."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='productPriceWiseSalesOnDate'){
    $payload["productPriceWiseSalesOnDate"] = queryGet("SELECT items.itemName AS item_name , SUM(items.basePrice - items.totalDiscountAmt) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.invoice_date = '".$specificDay."' GROUP BY item_name ORDER BY total_amount desc limit 10", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesOnMonth'){
    $payload["productQuantityWiseSalesOnMonth"] = queryGet("SELECT items.itemName AS item_name , SUM(items.qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.invoice_date BETWEEN '".$monthFromRange."' AND '".$monthToRange."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='productPriceWiseSalesOnMonth'){
    $payload["productPriceWiseSalesOnMonth"] = queryGet("SELECT items.itemName AS item_name , SUM(items.basePrice - items.totalDiscountAmt) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.invoice_date BETWEEN '".$monthFromRange."' AND '".$monthToRange."' GROUP BY item_name ORDER BY total_amount desc limit 10", true);

} else if($_GET['chart']=='dailyProfitCenterWiseSales'){
    $payload["dailyProfitCenterWiseSales"] = queryGet("SELECT date_format(invoices.invoice_date,'%Y-%m-%d') as date_, SUM(invoices.sub_total_amt - invoices.totalDiscount) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_id as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id AND so.branch_id=$branch_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND so_pc.profit_center='".$profitCenter."' GROUP BY date_ ORDER BY date_", true);
    
} else if($_GET['chart']=='monthlyProfitCenterWiseSales'){
    $payload["monthlyProfitCenterWiseSales"] = queryGet("SELECT date_format(invoices.invoice_date,'%M') as month, SUM(invoices.sub_total_amt - invoices.totalDiscount) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_id as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id AND so.branch_id=$branch_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND so_pc.profit_center='".$profitCenter."' GROUP BY month ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='yearlyProfitCenterWiseSales'){
    $payload["yearlyProfitCenterWiseSales"] = queryGet("SELECT so_pc.profit_center AS profit_center, SUM(invoices.sub_total_amt - invoices.totalDiscount) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id AND so.branch_id=$branch_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesProfitCenter'){
    $payload["productQuantityWiseSalesProfitCenter"] = queryGet("SELECT so_pc.profit_center AS profit_center,items.itemName AS item_name,SUM(items.qty) AS total_qty FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id AND so.branch_id=$branch_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center,item_name ORDER BY profit_center,total_qty DESC", true);
    
} else if($_GET['chart']=='productPriceWiseSalesProfitCenter'){
    $payload["productPriceWiseSalesProfitCenter"] = queryGet("SELECT so_pc.profit_center AS profit_center,items.itemName AS item_name,SUM(items.basePrice - items.totalDiscountAmt) AS total_price FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id AND so.branch_id=$branch_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center,item_name ORDER BY profit_center,total_price DESC", true);

} else if($_GET['chart']=='kamWiseQuantityProductGroupSales'){
    $payload["kamWiseQuantityProductGroupSales"] = queryGet("SELECT table2.kamName AS kam_name,table1.goodGroupName,SUM(table2.qty) AS total_qty FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.basePrice - items.totalDiscountAmt,invoices.kamId,kam.kamName FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_kam AS kam ON invoices.kamId = kam.kamId WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY kam_name,table1.goodGroupName ORDER BY total_qty desc", true);
    
} else if($_GET['chart']=='kamWisePriceProductGroupSales'){
    $payload["kamWisePriceProductGroupSales"] = queryGet("SELECT table2.kamName AS kam_name,table1.goodGroupName,SUM(table2.basePrice - table2.totalDiscountAmt) AS total_price FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.basePrice, items.totalDiscountAmt,invoices.kamId,kam.kamName FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_kam AS kam ON invoices.kamId = kam.kamId WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY kam_name,table1.goodGroupName ORDER BY total_price desc", true);
    
} else if($_GET['chart']=='kamWiseReceivables'){
    $payload["kamWiseReceivables"] = queryGet("SELECT erp_kam.kamName,table1.due_days,table1.count_,total_due_amount FROM (SELECT kamId,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY kamId,due_days) AS table1 LEFT JOIN erp_kam ON table1.kamId=erp_kam.kamId ORDER BY table1.due_days", true);

} else if($_GET['chart']=='customerWiseReceivables'){
    $payload["customerWiseReceivables"] = queryGet("SELECT erp_customer.trade_name,table1.due_days,table1.count_,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY table1.due_days", true);

} else if($_GET['chart']=='vendorWisePayables'){
    $payload["vendorWisePayables"] = queryGet("SELECT vendorName,DATEDIFF(dueDate,CURDATE())AS due_days,COUNT(*) AS count_,SUM(dueAmt) AS total_due_amount FROM erp_grninvoice AS iv WHERE companyId=$company_id AND branchId=$branch_id AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND dueAmt!=0 AND dueDate>CURDATE() GROUP BY vendorName,due_days ORDER BY due_days", true);

} else if($_GET['chart']=='salesOrderBook'){
    $payload["salesOrderBook"] = queryGet("SELECT status_.label, COUNT(*) AS count,SUM(totalAmount) AS total_amount FROM erp_branch_sales_order AS so LEFT JOIN erp_status_master AS status_ ON so.approvalStatus=status_.code WHERE so.company_id=$company_id AND so.branch_id=$branch_id AND so.so_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY status_.label ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='purchaseOrderBook'){
    $payload["purchaseOrderBook"] = queryGet("SELECT status_.label, COUNT(*) AS count,SUM(totalAmount) AS total_amount FROM erp_branch_purchase_order AS po LEFT JOIN erp_status_master AS status_ ON po.po_status=status_.code WHERE po.company_id=$company_id AND po.branch_id=$branch_id AND po.po_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY status_.label ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='salesVsCollection'){
    $payload["salesVsCollection"] = [    
        "revenue" => queryGet("SELECT invoice_date AS date_, SUM(sub_total_amt - totalDiscount) AS total_revenue , SUM(due_amount) AS receivable FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_ ORDER BY date_;", true),
        "collection" => queryGet("SELECT postingDate AS date_, SUM(collect_payment) AS collection FROM erp_branch_sales_order_payments WHERE company_id=$company_id AND branch_id=$branch_id AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_ ORDER BY date_;", true)
    ];
    
} else if($_GET['chart']=='financialKeyHighlights'){
    $payload["financialKeyHighlights"] = [
        "total_revenue" => queryGet("SELECT SUM(temp_table.amount) AS total_income FROM (SELECT coa.gl_code,coa.gl_label,debit.debit_amount AS amount FROM erp_acc_debit AS debit INNER JOIN erp_acc_journal AS journal ON debit.journal_id=journal.id LEFT JOIN erp_acc_coa_1_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.journal_status='active' AND journal.postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND coa.typeAcc=3 UNION SELECT coa.gl_code,coa.gl_label,credit.credit_amount AS amount FROM erp_acc_credit AS credit INNER JOIN erp_acc_journal AS journal ON credit.journal_id=journal.id LEFT JOIN erp_acc_coa_1_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.journal_status='active' AND journal.postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND coa.typeAcc=3) AS temp_table;", true),

        "financial_data" => queryGet("SELECT temp_table2.id,temp_table2.gl_code,temp_table2.gl_label,temp_table2.lvl,temp_table2.p_id,SUM(temp_table2.expense_amount) AS expence FROM (SELECT chart.id,chart.gl_code,chart.gl_label,chart.lvl,chart.p_id,temp_table.amount AS expense_amount FROM erp_acc_coa_1_table AS chart LEFT JOIN (SELECT coa.gl_code,coa.gl_label,SUM(debit.debit_amount) AS amount FROM erp_acc_debit AS debit INNER JOIN erp_acc_journal AS journal ON debit.journal_id=journal.id LEFT JOIN erp_acc_coa_1_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.journal_status='active' AND journal.postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND coa.typeAcc=4 GROUP BY coa.gl_code,coa.gl_label) AS temp_table ON chart.gl_code=temp_table.gl_code WHERE chart.typeAcc=4
        UNION
        SELECT chart.id,chart.gl_code,chart.gl_label,chart.lvl,chart.p_id,temp_table.amount AS expense_amount FROM erp_acc_coa_1_table AS chart LEFT JOIN (SELECT coa.gl_code,coa.gl_label,SUM(credit.credit_amount)*(-1) AS amount FROM erp_acc_credit AS credit INNER JOIN erp_acc_journal AS journal ON credit.journal_id=journal.id LEFT JOIN erp_acc_coa_1_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.journal_status='active' AND journal.postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND coa.typeAcc=4 GROUP BY coa.gl_code,coa.gl_label) AS temp_table ON chart.gl_code=temp_table.gl_code WHERE chart.typeAcc=4) AS temp_table2 GROUP BY temp_table2.id,temp_table2.gl_code,temp_table2.gl_label,temp_table2.lvl,temp_table2.p_id ORDER BY temp_table2.lvl desc;", true)
    ];
    
} else{};


// $payload["x"] = queryGet("", true);

// console($payload);

echo json_encode($payload, true);
