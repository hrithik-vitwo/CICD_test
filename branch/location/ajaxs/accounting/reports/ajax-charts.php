<?php
require_once("../../../app/v1/connection-company-admin.php");
$headerData = array('Content-Type: application/json');

//$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];

$payload = [];

$fromDate = "2022-04-01";
$toDate = "2023-03-31";
$itemCode = 22000001;
$profitCenter = "func 3";
$specificDay = "2023-02-14";
$monthFromRange = "2023-01-01";
$monthToRange = "2023-01-31";

// print_r($_GET);

// ADDING DATA TO PAYLOAD ON CONDITION
if($_GET['chart']=='dailyPeriodicSales'){
    $payload["dailyPeriodicSales"] = queryGet("SELECT invoice_date,SUM(all_total_amt) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active' GROUP BY invoice_date", true);

} else if($_GET['chart']=='monthlyPeriodicSales'){
    // $payload["monthlyPeriodicSales"] = queryGet("SELECT date_format(invoice_date,'%M') AS month,SUM(all_total_amt) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active' GROUP BY month", true);

} else if($_GET['chart']=='yearlyPeriodicSales'){
    // $payload["yearlyPeriodicSales"] = queryGet("SELECT SUM(all_total_amt) AS total_revenue FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND status='active'", true);

} else if($_GET['chart']=='dailyProductQuantityWiseSales'){
    $payload["dailyProductQuantityWiseSales"] = queryGet("SELECT  date_format(items.createdAt,'%Y-%m-%d') AS date_ , items.itemName AS item_name , SUM(qty) AS total_qty  FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_,item_name ORDER BY date_", true);

} else if($_GET['chart']=='dailyProductPriceWiseSales'){
    $payload["dailyProductPriceWiseSales"] = queryGet("SELECT date_format(items.createdAt,'%Y-%m-%d') AS date_ , items.itemName AS item_name , SUM(items.totalPrice) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_,item_name ORDER BY date_", true);

} else if($_GET['chart']=='monthlyProductQuantityWiseSales'){
    $payload["monthlyProductQuantityWiseSales"] = queryGet("SELECT  date_format(items.createdAt,'%M') AS month , items.itemName AS item_name , SUM(qty) AS total_qty  FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY month,item_name ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='monthlyProductPriceWiseSales'){
    $payload["monthlyProductPriceWiseSales"] = queryGet("SELECT date_format(items.createdAt,'%M') AS month , items.itemName AS item_name , SUM(items.totalPrice) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND items.itemCode=$itemCode AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY month,item_name ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='yearlyProductQuantityWiseSales'){
    $payload["yearlyProductQuantityWiseSales"] = queryGet("SELECT items.itemName AS item_name , SUM(items.qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='yearlyProductPriceWiseSales'){
    $payload["yearlyProductPriceWiseSales"] = queryGet("SELECT items.itemName AS item_name , SUM(items.totalPrice) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND createdAt BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY item_name Order By total_amount desc limit 10", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesOnDate'){
    $payload["productQuantityWiseSalesOnDate"] = queryGet("SELECT items.itemName AS item_name , SUM(items.qty) AS total_qty  FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.invoice_date = '".$specificDay."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='productPriceWiseSalesOnDate'){
    $payload["productPriceWiseSalesOnDate"] = queryGet("SELECT items.itemName AS item_name , SUM(items.totalPrice) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.invoice_date = '".$specificDay."' GROUP BY item_name ORDER BY total_amount desc limit 10", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesOnMonth'){
    $payload["productQuantityWiseSalesOnMonth"] = queryGet("SELECT items.itemName AS item_name , SUM(items.qty) AS total_qty FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$monthFromRange."' AND '".$monthToRange."' GROUP BY item_name ORDER BY total_qty desc limit 10", true);
    
} else if($_GET['chart']=='productPriceWiseSalesOnMonth'){
    $payload["productPriceWiseSalesOnMonth"] = queryGet("SELECT items.itemName AS item_name , SUM(items.totalPrice) AS total_amount FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$monthFromRange."' AND '".$monthToRange."' GROUP BY item_name ORDER BY total_amount desc limit 10", true);
    
} else if($_GET['chart']=='dailyProfitCenterWiseSales'){
    $payload["dailyProfitCenterWiseSales"] = queryGet("SELECT date_format(invoices.invoice_date,'%Y-%m-%d') as date_, SUM(invoices.all_total_amt) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND so_pc.profit_center='".$profitCenter."' GROUP BY date_ ORDER BY date_", true);
    
} else if($_GET['chart']=='monthlyProfitCenterWiseSales'){
    $payload["monthlyProfitCenterWiseSales"] = queryGet("SELECT date_format(invoices.invoice_date,'%M') as month, SUM(invoices.all_total_amt) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=1) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND so_pc.profit_center='".$profitCenter."' GROUP BY month ORDER BY STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')", true);
    
} else if($_GET['chart']=='yearlyProfitCenterWiseSales'){
    $payload["yearlyProfitCenterWiseSales"] = queryGet("SELECT so_pc.profit_center AS profit_center, SUM(invoices.all_total_amt) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='productQuantityWiseSalesProfitCenter'){
    $payload["productQuantityWiseSalesProfitCenter"] = queryGet("SELECT so_pc.profit_center AS profit_center,items.itemName AS item_name,SUM(items.qty) AS total_qty FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center,item_name ORDER BY profit_center,total_qty DESC", true);
    
} else if($_GET['chart']=='productPriceWiseSalesProfitCenter'){
    $payload["productPriceWiseSalesProfitCenter"] = queryGet("SELECT so_pc.profit_center AS profit_center,items.itemName AS item_name,SUM(items.totalPrice) AS total_price FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center,item_name ORDER BY profit_center,total_price DESC", true);

} else if($_GET['chart']=='stateWiseSalesProfitCenter'){
    $payload["stateWiseSalesProfitCenter"] = queryGet("SELECT table1.profit_center,table1.branch_id,branch.state AS state,table1.total_amount FROM (SELECT so_pc.profit_center AS profit_center,invoices.branch_id AS branch_id, SUM(invoices.all_total_amt) AS total_amount FROM (SELECT so.so_number as so_number,pc.functionalities_name as profit_center FROM erp_branch_sales_order AS so LEFT JOIN erp_company_functionalities as pc ON so.profit_center=pc.functionalities_id WHERE so.company_id=$company_id) AS so_pc INNER JOIN erp_branch_sales_order_invoices AS invoices ON so_pc.so_number=invoices.so_number WHERE invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY profit_center,branch_id) AS table1 LEFT JOIN erp_branches AS branch ON table1.branch_id = branch.branch_id ORDER BY profit_center,total_amount DESC", true);
    
} else if($_GET['chart']=='kamWiseQuantityProductGroupSales'){
    $payload["kamWiseQuantityProductGroupSales"] = queryGet("SELECT table2.kamName AS kam_name,table1.goodGroupName,SUM(table2.qty) AS total_qty FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.totalPrice,invoices.kamId,kam.kamName FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_kam AS kam ON invoices.kamId = kam.kamId WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY kam_name,table1.goodGroupName ORDER BY total_qty desc", true);
    
} else if($_GET['chart']=='kamWisePriceProductGroupSales'){
    $payload["kamWisePriceProductGroupSales"] = queryGet("SELECT table2.kamName AS kam_name,table1.goodGroupName,SUM(table2.totalPrice) AS total_price FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.totalPrice,invoices.kamId,kam.kamName FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_kam AS kam ON invoices.kamId = kam.kamId WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY kam_name,table1.goodGroupName ORDER BY total_price desc", true);
    
} else if($_GET['chart']=='stateWiseQuantityProductGroupSales'){
    $payload["stateWiseQuantityProductGroupSales"] = queryGet("SELECT table2.branch_id,table2.state,table1.goodGroupName,SUM(table2.qty) AS total_qty FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.totalPrice,invoices.branch_id,branch.state FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_branches AS branch ON invoices.branch_id=branch.branch_id WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY table2.branch_id,table2.state,table1.goodGroupName ORDER BY total_qty desc", true);
    
} else if($_GET['chart']=='stateWisePriceProductGroupSales'){
    $payload["stateWisePriceProductGroupSales"] = queryGet("SELECT table2.branch_id,table2.state,table1.goodGroupName,SUM(table2.totalPrice) AS total_price FROM (SELECT items.itemCode,items.itemName,groups.goodGroupName FROM erp_inventory_items AS items INNER JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup=groups.goodGroupId WHERE items.company_id=$company_id) AS table1 INNER JOIN (SELECT items.itemCode,items.qty,items.totalPrice,invoices.branch_id,branch.state FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_branches AS branch ON invoices.branch_id=branch.branch_id WHERE invoices.company_id=$company_id AND invoices.invoice_date BETWEEN '".$fromDate."' AND '".$toDate."') AS table2 ON table1.itemCode = table2.itemCode GROUP BY table2.branch_id,table2.state,table1.goodGroupName ORDER BY total_price desc", true);
    
} else if($_GET['chart']=='kamWiseReceivables'){
    $payload["kamWiseReceivables"] = queryGet("SELECT erp_kam.kamName,table1.due_days,table1.count_,total_due_amount FROM (SELECT kamId,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND invoiceStatus IN(1,2,3) AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY kamId,due_days) AS table1 LEFT JOIN erp_kam ON table1.kamId=erp_kam.kamId ORDER BY table1.due_days", true);
    
} else if($_GET['chart']=='customerWiseReceivables'){
    $payload["customerWiseReceivables"] = queryGet("SELECT erp_customer.trade_name,table1.due_days,table1.count_,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' AND invoiceStatus IN(1,2,3) AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY table1.due_days", true);
    
} else if($_GET['chart']=='vendorWisePayables'){
    $payload["vendorWisePayables"] = queryGet("SELECT vendorName,DATEDIFF(dueDate,CURDATE())AS due_days,COUNT(*) AS count_,SUM(dueAmt) AS total_due_amount FROM erp_grninvoice AS iv WHERE companyId=$company_id AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND paymentStatus IN (1,2,3) AND dueDate>CURDATE() GROUP BY vendorName,due_days ORDER BY due_days", true);
    
} else if($_GET['chart']=='salesOrderBook'){
    $payload["salesOrderBook"] = queryGet("SELECT status_.label, COUNT(*) AS count, SUM(totalAmount) AS total_amount FROM erp_branch_sales_order AS so LEFT JOIN erp_status_master AS status_ ON so.approvalStatus=status_.code WHERE so.company_id=$company_id AND so.so_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY status_.label ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='purchaseOrderBook'){
    $payload["purchaseOrderBook"] = queryGet("SELECT status_.label, COUNT(*) AS count, SUM(totalAmount) AS total_amount FROM erp_branch_purchase_order AS po LEFT JOIN erp_status_master AS status_ ON po.po_status=status_.code WHERE po.company_id=$company_id AND po.po_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY status_.label ORDER BY total_amount DESC", true);
    
} else if($_GET['chart']=='salesVsCollection'){
    $payload["salesVsCollection"] = [    
        "revenue" => queryGet("SELECT invoice_date AS date_, SUM(all_total_amt) AS total_revenue , SUM(due_amount) AS receivable FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND invoice_date BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_ ORDER BY date_;", true),
        "collection" => queryGet("SELECT postingDate AS date_, SUM(collect_payment) AS collection FROM erp_branch_sales_order_payments WHERE company_id=$company_id AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' GROUP BY date_ ORDER BY date_;", true)
    ];
    
} else{};


// $payload["x"] = queryGet("", true);

// console($payload);

echo json_encode($payload, true);
