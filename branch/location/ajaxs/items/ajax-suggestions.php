<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if($_SERVER['REQUEST_METHOD'] === 'GET')  {

   $item_name = $_GET['item_name'];
   if($item_name == ""){
    $responseData['item_sugg']="";
    echo json_encode($responseData);
   }
   else{

   $duplicate = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemName` = '".$item_name."' AND `company_id`=$company_id",true);
   if($duplicate['numRows'] > 0){
    $responseData['duplicate'] = 1;
 
}
else{
    $responseData['duplicate'] = 0;  
}

   
    $sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemName` LIKE '%$item_name%' AND `company_id`=$company_id",true);
   
    $array = [];
    foreach($sql['data'] as $data){
       $item = $data['itemName'];
        $array[] = array('item'=>$item);
    }
   // $array
   $item_sugg='';

 
 
   
   foreach($array as $arr){
    $item_sugg.='<li class="text-xs liClass" id="'.$arr['item'].'">'.$arr['item'].'</li>';

   }
   $responseData['item_sugg']=$item_sugg;
 
echo json_encode($responseData);
}
}
else{
    echo 0;

}


?>