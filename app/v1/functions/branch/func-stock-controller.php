<?php

class StockController{
    
    function getStockDeatils($itemId=0, $stockTypeSlug=""){
        global $company_id;
        global $branch_id;
        global $location_id;

        $stockSummaryTableName = "erp_inventory_stocks_summary";

        if($stockTypeSlug!=""){
            $queryObj = queryGet('SELECT `'.$stockTypeSlug.'` FROM `'.$stockSummaryTableName.'` WHERE `company_id`=' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `location_id`= ' . $location_id . ' AND `itemId`=' . $itemId);
            if ($queryObj["numRows"] == 1) {
                return $queryObj["data"][$stockTypeSlug];
            } else {
                return [
                    $stockTypeSlug=>0
                ];
            }
        }else{
            $queryObj = queryGet('SELECT `rmWhOpen`, `rmWhReserve`, `rmProdOpen`, `rmProdReserve`, `sfgStockOpen`, `sfgStockReserve`, `fgWhOpen`, `fgWhReserve`, `fgMktOpen`, `fgMktReserve` FROM `'.$stockSummaryTableName.'` WHERE `company_id`=' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `location_id`= ' . $location_id . ' AND `itemId`=' . $itemId);
            if ($queryObj["numRows"] == 1) {
                return $queryObj["data"];
            } else {
                return [
                    'rmWhOpen'=>0, "rmWhReserve"=>0, "rmProdOpen"=>0, "rmProdReserve"=>0, "sfgStockOpen"=>0, "sfgStockReserve"=>0, "fgWhOpen"=>0, "fgWhReserve"=>0, "fgMktOpen"=>0, "fgMktReserve"=>0
                ];
            }
        }
    }

}

?>