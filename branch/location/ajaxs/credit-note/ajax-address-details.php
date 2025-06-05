<?php

require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_GET['act'] === "address") {
     $id = $_GET['id'];
     $type = $_GET['dataAttrVal']; //check if customer or vendor

     if($type == "customer"){

        $customer_sql = queryGet("SELECT * FROM  `erp_customer_address` WHERE `customer_id` = $id",true);
        $dest_address='';
      
        foreach($customer_sql['data'] as $data){
           $dest_address .= '<option value="'.$data['customer_address_state_code'].'">'.$data['customer_address_state'].'</option>';
         }

     $company_address_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = $location_id ",true);
     $supply_address ='';
     // console($company_address_sql);

     foreach($company_address_sql['data'] as $company_data){
        $supply_address .= '<option value="'.$company_data['state_code'].'">'.$company_data['othersLocation_state'].'</option>';
        }


        
     $inv_sql = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `customer_id` = $id LIMIT 50",true);
     $invoice ='<option>select invoice</option>';

     foreach($inv_sql['data'] as $data){
        $invoice .= '<option value="'.$data['so_invoice_id'].'" data-attr="inv">'.$data['invoice_no'].'</option>';
     }

$address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id` =  $id AND `customer_address_primary_flag` = 1");

     $address = $address_sql['data']['customer_address_name'].','.$address_sql['data']['customer_address_building_no'].','.$address_sql['data']['customer_address_flat_no'].','.$address_sql['data']['customer_address_street_name'].','.$address_sql['data']['customer_address_pin_code'].','.$address_sql['data']['customer_address_location'].','.$address_sql['data']['customer_address_city'].','.$address_sql['data']['customer_address_district'].','.$address_sql['data']['customer_address_state'];

      
     $responseData['bill_address_id'] = $address_sql['data']['customer_address_id'];
 
     $responseData['shipping_address_id'] =  $address_sql['data']['customer_address_id'];
 
     $responseData['bill_address'] = $address;
 
     $responseData['shipping_address'] = $address;


     }
     elseif($type == "vendor"){


        $vendor_sql = queryGet("SELECT * FROM  `erp_vendor_bussiness_places` WHERE `vendor_id` = $id",true);
        $supply_address='';
      
        foreach($vendor_sql['data'] as $data){
           $supply_address .= '<option value="'.$data['state_code'].'">'.$data['vendor_business_state'].'</option>';
         }

     $company_address_sql = queryGet("SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id` =$location_id",true);
     $dest_address ='';

     foreach($company_address_sql['data'] as $company_data){
        $dest_address .= '<option value="'.$company_data['state_code'].'">'.$company_data['othersLocation_state'].'</option>';
        }


    
        $inv_sql = queryGet("SELECT * FROM `erp_grninvoice` WHERE `vendorId` = $id LIMIT 50",true);

        $invoice ='<option>select invoice</option>';
   
        foreach($inv_sql['data'] as $data){
           $invoice .= '<option value="'.$data['grnIvId'].'" data-attr="grn">'.$data['grnIvCode'].'</option>';
        }


        $location_address = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = $location_id ");

        $address = $location_address['data']['othersLocation_name'].','.$location_address['data']['othersLocation_building_no'].','.$location_address['data']['othersLocation_flat_no'].','.$location_address['data']['othersLocation_street_name'].','.$location_address['data']['othersLocation_pin_code'].','.$location_address['data']['othersLocation_location'].','.$location_address['data']['othersLocation_city'].','.$location_address['data']['othersLocation_district'].','.$location_address['data']['othersLocation_state'];

      
    $responseData['bill_address_id'] = $location_id;

    $responseData['shipping_address_id'] =  $location_id;

    $responseData['bill_address'] = $address;

    $responseData['shipping_address'] = $address;



     }
     else{

     }

$responseData['supply_address'] = $supply_address;
$responseData['destination_address'] = $dest_address;
$responseData['invoice'] = $invoice;


echo json_encode($responseData);

}



?>