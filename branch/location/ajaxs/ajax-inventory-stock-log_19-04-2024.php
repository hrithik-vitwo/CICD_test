<?php
require_once("../../../app/v1/connection-branch-admin.php");
$returnResponse=[];

if (isset($_GET['act']) && $_GET['act'] == 'stock-log') {
    $itemId = $_GET['itemId'];
    $ddate = $_GET['ddate'];
// AND LOG.postingDate <= '".$ddate."'


    $sql = queryGet("SELECT                                             loc.othersLocation_name AS location,
                                                                        LOG.refNumber AS document_no,
                                                                        items.itemCode,
                                                                        items.itemName,
                                                                        LOG.postingDate as Ddate,
                                                                        grp.goodGroupName AS itemGroup,
                                                                        str_loc.storage_location_name AS storage_location,
                                                                        LOG.logRef,
                                                                        CASE 
                                                                            WHEN LOG.refActivityName IN ('GRN', 'REV-GRN') THEN grn.vendorCode 
                                                                            WHEN LOG.refActivityName IN ('INVOICE', 'REV-INVOICE', 'DELIVERY', 'REV-DELIVERY', 'PGI') THEN customer.customer_code
                                                                            ELSE 'INTERNAL'
                                                                            END AS party_code,
                                                                        CASE 
                                                                            WHEN LOG.refActivityName IN ('GRN', 'REV-GRN') THEN grn.vendorName
                                                                            WHEN LOG.refActivityName IN ('INVOICE', 'REV-INVOICE', 'DELIVERY', 'REV-DELIVERY', 'PGI') THEN customer.trade_name
                                                                            ELSE 'INTERNAL'
                                                                            END AS party_name,
                                                                        UOM.uomName AS uom,
                                                                        LOG.refActivityName AS movement_type,
                                                                        LOG.itemQty AS qty,
                                                                        LOG.itemPrice AS rate,
                                                                        LOG.itemPrice * LOG.itemQty AS value,
                                                                        curr.currency_name AS currency
                                                                    FROM
                                                                        erp_inventory_stocks_log AS LOG
                                                                    LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId
                                                                    LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId
                                                                    LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId
                                                                    LEFT JOIN erp_branch_otherslocation AS loc ON LOG.locationId = loc.othersLocation_id
                                                                    LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId
                                                                    LEFT JOIN erp_companies AS comp ON LOG.companyId = comp.company_id
                                                                    LEFT JOIN erp_currency_type AS curr ON comp.company_currency = curr.currency_id
                                                                    LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id 
                                                                    LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode
                                                                    LEFT JOIN erp_branch_sales_order_invoice_items AS inv_itm ON items.itemId = inv_itm.inventory_item_id
                                                                    LEFT JOIN erp_branch_sales_order_invoices AS inv ON inv_itm.so_invoice_id = inv.so_invoice_id
                                                                    LEFT JOIN erp_customer AS customer ON inv.customer_id = customer.customer_id
                                                                    WHERE
                                                                        items.itemId ='" . $itemId . "' AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id AND LOG.postingDate = '".$ddate."'
                                                                    GROUP BY
                                                                        items.itemId,
                                                                        LOG.logRef,
                                                                        LOG.refNumber,
                                                                        str_loc.storage_location_name,
                                                                        grn.vendorCode,
                                                                        grn.vendorName,
                                                                        customer.customer_code,
                                                                        customer.trade_name,
                                                                        UOM.uomName,
                                                                        movement_type,
                                                                        LOG.itemQty,
                                                                        curr.currency_name,
                                                                        LOG.itemPrice,
                                                                        LOG.postingDate
                                                                    ", true);
	
	$returnResponse=$sql['data'];
}
else{
	$returnResponse = [
    	"status"=> "error",
    	"message"=> "Something went wrong try again!",
    	"data"=> []
    ];
}
echo json_encode($returnResponse);

