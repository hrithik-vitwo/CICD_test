<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $customer_id = $_GET['customer_id'];
    $item_id = $_GET['item_id'];

    $sql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = $customer_id AND caddress.customer_address_primary_flag = 1");
    $taret_price_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId` = $item_id AND `location_id` = $location_id");
    $target_price = $taret_price_sql['data']['itemPrice'];
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
            echo $target_price;

        }

        else{

           // echo $territory;
            $sql_count = queryGet("SELECT count(*) as count FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
      // console($sql_count);

        $count = (int)$sql_count['data']['count'];
        if ($count > 0) {
            if ($count > 1) {


                if ($comapny_mrp_priority == 'territory') {
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND  varient.territory = $territory AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
                
                } else {
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND varient.customer_group = $mrp_group  AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
                }
            } else {
              //  echo 'okayyyyy';

                $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");
            }

    // console($mrp_sql);
            echo $mrp_sql['data']['mrp'];
        } else {
            echo $target_price;
        }
    
    }
}
