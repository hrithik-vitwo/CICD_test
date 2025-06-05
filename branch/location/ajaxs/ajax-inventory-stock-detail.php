<?php


require_once("../../../app/v1/connection-branch-admin.php");
$dbObj = new Database();

$returnResponse=[];
if (isset($_GET['act']) && $_GET['act'] == 'stock-detail') {
	$itemId=$_GET['itemId'];
	$dDate=$_GET['dDate'];

    // echo $itemId;
    // exit();

// 	 $sql="SELECT 
//         item.itemCode, 
//         item.itemName,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'rmWhOpen' THEN report.total_closing_qty ELSE 0 END) AS rmWhOpen_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'rmWhReserve' THEN report.total_closing_qty ELSE 0 END) AS rmWhReserve_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'rmProdOpen' THEN report.total_closing_qty ELSE 0 END) AS rmProdOpen_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'rmProdReserve' THEN report.total_closing_qty ELSE 0 END) AS rmProdReserve_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'sfgStockOpen' THEN report.total_closing_qty ELSE 0 END) AS sfgStockOpen_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'sfgStockReserve' THEN report.total_closing_qty ELSE 0 END) AS sfgStockReserve_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'fgWhOpen' THEN report.total_closing_qty ELSE 0 END) AS fgWhOpen_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'fgWhReserve' THEN report.total_closing_qty ELSE 0 END) AS fgWhReserve_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'fgMktOpen' THEN report.total_closing_qty ELSE 0 END) AS fgMarketOpen_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'fgMktReserve' THEN report.total_closing_qty ELSE 0 END) AS fgMarketReserve_qty,
//         SUM(CASE WHEN loc.storageLocationTypeSlug = 'QaLocation' THEN report.total_closing_qty ELSE 0 END) AS QaLocation_qty, 
//      SUM(report.total_closing_qty) AS total
//     FROM 
//         erp_inventory_stocks_log_report AS report
//     INNER JOIN (
//         SELECT 
//             item_id,
//             storage_id,
//             MAX(report_date) AS max_date
//         FROM 
//             erp_inventory_stocks_log_report
//         WHERE
//             report_date <= '".$dDate."'
//         GROUP BY 
//             item_id, storage_id
//     ) AS max_dates
//     ON 
//         report.item_id = max_dates.item_id
//         AND report.storage_id = max_dates.storage_id
//         AND report.report_date = max_dates.max_date
// LEFT JOIN erp_inventory_items AS item ON report.item_id = item.itemId
// LEFT JOIN erp_storage_location AS loc ON report.storage_id = loc.storage_location_id
// WHERE item.itemId = '".$itemId."' AND report.company_id='".$company_id."' AND report.branch_id='".$branch_id."' AND report.location_id='".$location_id."'
// GROUP BY 
//         report.item_id
// ";
$sql="SELECT 
            s.storage_location_type AS Type,
            COALESCE(SUM(CASE WHEN s.storage_location_storage_type = 'Open' THEN l.itemQty ELSE 0 END), 0) AS Open,
            COALESCE(SUM(CASE WHEN s.storage_location_storage_type = 'Reserve' THEN l.itemQty ELSE 0 END), 0) AS Reserve
        FROM 
            erp_storage_location AS s
        LEFT JOIN
            erp_inventory_stocks_log AS l ON l.storageLocationId = s.storage_location_id AND l.itemId = $itemId
        WHERE postingDate <= '".$dDate."' AND s.company_id=$company_id AND l.companyId = $company_id
        GROUP BY 
            s.storage_location_type";


	$query=$dbObj->queryGet($sql,true);
   
	$returnResponse=$query;




}else{
	$returnResponse = [
    	"status"=> "error",
    	"message"=> "Something went wrong try again!",
    	"data"=> []
    ];
}
echo json_encode($returnResponse);

