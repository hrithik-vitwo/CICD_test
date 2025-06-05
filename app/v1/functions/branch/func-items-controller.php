<?php
class ItemsController
{

    function createItems($INPUTS)
    {
        global $dbCon;
        $returnData = [];
        $isValidate = validate($INPUTS, [
            "goodTypeName" => "required",
            "goodTypeDesc" => "required"
        ], [
            "goodTypeName" => "Enter good type name",
            "goodTypeDesc" => "Enter good type  desc"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $companyId = $INPUTS["companyId"];
        $goodTypeName = $INPUTS["goodTypeName"];
        $goodTypeDesc = $INPUTS["goodTypeDesc"];

        $goodTypeCreatedBy = 1;

        $createSql = "INSERT INTO `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` SET `companyId`='" . $companyId . "',`goodTypeName`='" . $goodTypeName . "',`goodTypeDesc`='" . $goodTypeDesc . "',`goodTypeCreatedBy`='" . $goodTypeCreatedBy . "',`goodTypeUpdatedBy`='" . $goodTypeCreatedBy . "'";

        if (mysqli_query($dbCon, $createSql)) {
            $returnData["status"] = "success";
            $returnData["message"] = "Good type created success.";
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Good type created failed, try again!";
        }
        return $returnData;
    }

    // function getAllItems(){
    //     global $dbCon;
    //     $returnData = [];
    //     $selectSql = "SELECT * FROM `".ERP_INVENTORY_ITEMS."`";

    //     if($res = mysqli_query($dbCon, $selectSql)){
    //         $returnData['status'] = "success";
    //         $returnData['message'] = mysqli_num_rows($res) ." records found.";
    //         $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    //     }else{
    //         $returnData['status'] = "warning";
    //         $returnData['message'] = "Something went wrong, try again";
    //         $returnData['data'] = [];
    //     }
    //     return $returnData;
    // }

    function getAllItems()
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        // return $selectSql;
        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }



    function getAllItemsByGroupId($itemId)
    {

        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];

        $selectSql = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' AND goods.goodsGroup = $itemId ORDER BY stock.stockSummaryId desc";
        // return $selectSql;
        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function getAllItemsPo($type)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        // $selectSql = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `goodsType`!=3  AND company_id = '" . $company_id . "' AND `status`!='deleted'";
        /*--------
        1- RM
        2-SFG
        3- FG  
        4- FG(Trading)    
        5- SERVICE
        9- ASSET
        ----*/
        if ($type == "material") {
             $selectSql = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.company_id=$company_id AND (goods.goodsType=1 OR goods.goodsType=4 OR goods.goodsType=7)  AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY goods.itemCode ASC";
        } elseif ($type == "servicep") {
            $selectSql = " SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=7  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY goods.itemCode ASC";
        } elseif ($type == "asset") {
            $selectSql = " SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=9  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY goods.itemCode ASC";
        }
        // elseif($type=="fg"){
        //     $selectSql = " SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `".ERP_INVENTORY_ITEMS."` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=4  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        // }
        // elseif($type=="sfg"){
        //     $selectSql = " SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `".ERP_INVENTORY_ITEMS."` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=2  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        // }
        else {
            $selectSql = " SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE (goods.goodsType=1  OR goods.goodsType=4  OR goods.goodsType=5 OR goods.goodsType=9)  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != ''  ORDER BY goods.itemCode ASC";
        }
        // echo $selectSql;
        // $selectSql = " SELECT * FROM `".ERP_INVENTORY_STOCKS_SUMMARY."` as stock LEFT JOIN `".ERP_INVENTORY_ITEMS."` as goods ON stock.itemId=goods.itemId WHERE (goods.goodsType=1  OR goods.goodsType=4  OR goods.goodsType=5 OR goods.goodsType=9)  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function getAllItemsByType($id)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `goodsType`='" . $id . "' AND company_id = '" . $company_id . "' AND `status`!='deleted'";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }



    function getItemById($id)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $returnData['sql'] = $selectSql = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $id . "' AND company_id = '" . $company_id . "'  AND `status`!='deleted'";
        // $returnData['sql']=$selectSql = "SELECT items.*,uom.uomName as base_unit , uom.uomName as issue_unit from erp_inventory_items as items , erp_inventory_mstr_uom as uom WHERE items.baseUnitMeasure = uom.uomId AND items.issueUnitMeasure=uom.uomId AND items.itemId = '$id'";
        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }


    function getItemBomDetail($id)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $returnData = queryGet("SELECT * FROM `erp_bom` WHERE itemId='" . $id . "' AND companyId = '" . $company_id . "' AND branchId = '$branch_id' AND locationId = '$location_id'  AND `bomStatus`='active' ORDER BY bomId DESC LIMIT 1");

        return $returnData;
    }



    function getInvoiceItemDetail($invoiceId, $id)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $returnData = queryGet("SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE so_invoice_id=$invoiceId AND inventory_item_id='" . $id . "' LIMIT 1");

        return $returnData;
    }

    function getItemForPR($id)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $returnData['sql'] = $selectSql = "SELECT t1.*, t2.*, t3.*, t4.* FROM (SELECT uomName AS base_unit FROM erp_inventory_items LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_items.baseUnitMeasure = erp_inventory_mstr_uom.uomId WHERE erp_inventory_items.itemId = '$id') AS t2 , (SELECT uomName AS issue_unit FROM erp_inventory_items LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_items.issueUnitMeasure = erp_inventory_mstr_uom.uomId WHERE erp_inventory_items.itemId = '$id') AS t3 , (SELECT uomName AS serviceUnit FROM erp_inventory_items LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_items.service_unit = erp_inventory_mstr_uom.uomId WHERE erp_inventory_items.itemId = '$id') AS t4 , erp_inventory_items AS t1 WHERE t1.itemId = '$id' AND company_id = '$company_id'  AND branch = '$branch_id' AND location_id = '$location_id' AND `status`!='deleted'";
        // $returnData['sql']=$selectSql = "SELECT items.*,uom.uomName as base_unit , uom.uomName as issue_unit from erp_inventory_items as items , erp_inventory_mstr_uom as uom WHERE items.baseUnitMeasure = uom.uomId AND items.issueUnitMeasure=uom.uomId AND items.itemId = '$id'";
        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch item details by itemCode
    function getItemDetailsByCode($itemCode)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $returnData['sql'] = $selectSql = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemCode`='$itemCode' AND company_id = '" . $company_id . "'  AND branch = '" . $branch_id . "' AND location_id = '" . $location_id . "' AND `status`!='deleted'";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = $res->num_rows . " records found.";
            $returnData['data'] = $res->fetch_assoc();
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function getBaseUnitMeasureById($id)
    {
        global $dbCon;
        $returnData = [];
        $returnData['sql'] = $selectSql = "SELECT * FROM `" . ERP_INVENTORY_MASTER_UOM . "` WHERE uomId='$id'";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function mrp($customer_id, $item_id)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        //movingWeightedPrice
$taret_price_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId` = $item_id AND `location_id` = $location_id");
$target_price = $taret_price_sql['data']['itemPrice'];


        $sql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = $customer_id AND caddress.customer_address_primary_flag = 1");

    // console($sql);
        $customer_state = $sql['data']['customer_address_state_code'];

        $query = "SELECT * FROM erp_mrp_territory WHERE `location_id` = $location_id" ;
        $result = queryGet($query,true);
        //console($result);
        // Define an array to store the matching rows
        $matching_rows = [];
        
        foreach ($result['data'] as $row) {
            // console($row); 
            // console($row['state_codes']);
            $state_codes = unserialize($row['state_codes']);
            // console($state_codes);
        
            // Check if $customer_state exists in the unserialized array
            if (in_array($customer_state, $state_codes)) {
                $matching_rows[] = $row;
            } 
        }
          //  console($matching_rows[0]);
            $territory = !empty($matching_rows[0]['territory_id']) ? $matching_rows[0]['territory_id'] : 0;
            $mrp_group =  !empty($sql['data']['customer_mrp_group']) ? $sql['data']['customer_mrp_group'] :0;
        
            //let us assume 
          //  $comapny_mrp_priority = 'territory';
        
        
          $company_sql = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = $company_id");
         $comapny_mrp_priority = $company_sql['data']['mrpPriority'];
        $today = date('Y-m-d');
        // echo 'okayyyyyy';
        // echo $territory;
        // echo 'll';

        if($territory == 0 && $mrp_group == 0){
          //  echo 'ok';
            return $target_price;

        }

        else{

           // echo $territory;
            $sql_count = queryGet("SELECT count(*) as count FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
       //console($sql_count);

        $count = (int)$sql_count['data']['count'];
        if ($count > 0) {
            if ($count > 1) {
// echo $comapny_mrp_priority;

                if ($comapny_mrp_priority == 'territory') {
                    echo 0;
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND  varient.territory = $territory AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
                    
                
                } else {
                    echo 1;
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND varient.customer_group = $mrp_group  AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
                  
                }
            } else {
              //  echo 'okayyyyy';

                $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
            }

    //  console($mrp_sql);
    //  exit();
            return $mrp_sql['data']['mrp'];
        } else {
            return $target_price;
        }
    
    }
}
}
