<?php
    include("../../../app/v1/functions/common/func-common.php");
    require_once("api-common-func.php");


    class StockLogUpload
    {
    function uploadStockLog($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        $created_by = $user_id."|location";
        $updated_by = $user_id."|location";
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;


        foreach($POST_DATA as $POST)
        {

            $storage_location_code = $POST["storage_location_code"];

            $storage_location_query = queryGet("SELECT * FROM `erp_storage_location` WHERE `company_id` = '" . $company_id . "' AND `branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `storage_location_code`='".$storage_location_code."'",false);

            if($storage_location_query["numRows"] == 0)
            {
                $returnData['status'] = "warning";
                $returnData['message'] = "Storage Location not exists";
                $flag[] = array("status"=>"warning","message"=>"Storage Location not exists at line ".$i,"query"=>$storage_location_query);
                $i++;
                $error_flag++;
                continue;
            }
            else
            {
                $oneItemStorageLocationId = $storage_location_query["data"]["storage_location_id"];
                $oneItemStorageLocationType = $storage_location_query["data"]["storage_location_type"];
                $storageLocationTypeSlug = $storage_location_query["data"]["storageLocationTypeSlug"];

                $item_code = $POST["item_code"];

                $item_query = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = '" . $company_id . "' AND `branch`='".$branch_id."' AND `location_id`='".$location_id."' AND `itemCode`='".$item_code."'",false);
                if($item_query["numRows"] == 0)
                {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Item not exists";
                    $flag[] = array("status"=>"warning","message"=>"Item not exists at line ".$i);
                    $i++;
                    $error_flag++;
                    continue;
                }
                else
                {
                
                    $oneItemId = $item_query["data"]["itemId"];
                    $oneItemStocksQty = $POST["oneItemStocksQty"] != "" ? $POST["oneItemStocksQty"] : 0;
                    $uom = strtolower(trim($POST["oneItemUom"]));
                    $proper_uom = $POST["oneItemUom"];
                    $oneItemUomQuery = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE (`companyId` = '" . $company_id . "' OR `companyId` = '0') AND LOWER(`uomName`)='".$uom."'");

                    if($oneItemUomQuery["numRows"] == 0)
                    {
                        //Insert
                        $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                                        SET
                                            `companyId`='" . $company_id . "',
                                            `uomName`='".$proper_uom."',
                                            `uomDesc`='".$proper_uom."',
                                            `uomType`='material',
                                            `uomCreatedBy`='".$created_by."',
                                            `uomUpdatedBy`='".$created_by."'";
                        //exit();

                        $insertIteUom = queryInsert($insuom);

                        $oneItemUomId = $insertIteUom['insertedId'];
                    }
                    else
                    {
                        $oneItemUomId = $oneItemUomQuery["data"]["uomId"];
                    }

                    $oneItemUnitPrice = $POST["oneItemUnitPrice"] != "" ? $POST["oneItemUnitPrice"] : 0;

                    $logRef = "MIGRATION".time();

                    $opening_date_query = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = '" . $company_id."'",false);

                    $posting_date = $opening_date_query["data"]["opening_date"];

                    $born_date = $POST["born_date"] != "" ? date("Y-m-d", strtotime($POST["born_date"])) : $posting_date;

                    $get_stock_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `company_id` = '" . $company_id . "' AND `branch_id`= '".$branch_id."' AND `location_id`='".$location_id."' AND `itemId`='".$oneItemId."' ORDER BY `stockSummaryId` DESC LIMIT 1");

                    if($get_stock_summary["numRows"] == 0)
                    {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Item not added for this location";
                        $flag[] = array("status"=>"warning","message"=>"Item not added for this location at line ".$i);
                        $i++;
                        $error_flag++;
                        continue;
                    }
                    else
                    {

                        // $oneItemStocksQty = $grnItem["itemStockQty"] ?? 0.00; //500
                        // $oneItemUnitPrice = $grnItem["itemUnitPrice"] ?? 0.00; //50

                        $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId);

                        $prevTotalQty = $goodStockSummaryCheckSql["data"]["itemTotalQty"] ?? 0; //3000.00
                        $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0; //50.00
                        $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                        $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                        $itemNewTotalPrice = (float)$prevTotalPrice + ($oneItemStocksQty * $oneItemUnitPrice); //(150000 + (500 * 50)) //175000
                        $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                        if (is_nan($movingWeightedPrice)) {
                            $movingWeightedPrice = 0;
                        }

                        $update_stock_summary_query = 'UPDATE `erp_inventory_stocks_summary` SET `movingWeightedPrice`=' . $movingWeightedPrice . ', `itemTotalQty`="'.$itemNewTotalQty.'", `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneItemId;

                        $goodStockUpdatetObj = queryUpdate($update_stock_summary_query);

                        if ($goodStockUpdatetObj["status"] != "success") {
                            $returnData['status'] = "warning";
                            $returnData['message'] = "Stock Summary not updated";
                            $flag[] = array("status"=>"warning","message"=>"Stock Summary not updated at line ".$i);
                            $i++;
                            $error_flag++;
                            continue;
                        }
                        else
                        {
                            $stockLogInsertSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageType`="'.$storageLocationTypeSlug.'",`storageLocationId`=' . $oneItemStorageLocationId . ', `itemId`=' . $oneItemId . ',`itemQty`=' . $oneItemStocksQty . ',`itemUom`='.$oneItemUomId.',`itemPrice`=' . $oneItemUnitPrice . ', `refActivityName`="MIGRATION", `logRef`="' . $logRef . '", `refNumber`="' . $logRef . '", `bornDate`="' . $born_date . '", `postingDate`="'.$posting_date.'", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '";';
                            $stockLogInsertSqlInsert = queryInsert($stockLogInsertSql);
                            if ($stockLogInsertSqlInsert['status'] == 'success') {
                                $returnData['status'] = "success";
                                $returnData['message'] = "Stock Log added successfully";
                                $flag[] = array("status"=>"success","message"=>"Stock Log added successfully at line ".$i);
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message'] = "Stock Log added failed";
                                $flag[] = array("status"=>"warning","message"=>"Stock Log added failed at line ".$i,"query"=>$stockLogInsertSqlInsert);
                                $error_flag++;
                            }
                        }
                        }
                        
                        }
                    }
                
            $i++;
        }

        $total_array = array("flag"=>$flag,"error_flag"=>$error_flag);

        if($declaration == 0)
        {
            $declaration_value = 'unlock';
        }
        else
        {
            $declaration_value = 'lock';
        }

        $insvalidation = "INSERT INTO `erp_migration_validation`
                        SET 
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `user_id`='$user_id',
                            `migration_type`='stocklog',
                            `declaration`='$declaration_value',
                            `created_by`='$created_by',
                            `updated_by`='$created_by' 
                            ";
                            queryInsert($insvalidation);

        return $total_array;
    }
    }


?>