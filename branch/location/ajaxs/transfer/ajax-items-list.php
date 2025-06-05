<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

/*$ItemsObj = new ItemsController(); 
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    // console($_GET['deliveryDate']);
    $getItemObj = $ItemsObj->getItemById($itemId);
    //$boum = $ItemsObj
    $itemCode = $getItemObj['data']['itemCode'];
    $lastPricesql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `itemCode`=$itemCode ORDER BY po_item_id DESC LIMIT 1";
    $last = queryGet($lastPricesql);
    $lastRow = $last['data'] ?? "";
    $lastPrice = $lastRow['unitPrice'] ?? "";

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99); 
    $randDel = rand(00, 99);
   // console($randCode);
?>


<td>
<select name="" class="select2 form-control">
        <option value="">UOM</option>
        <option value="">KG</option>
        <option value="">PCS</option>
</select>       
</td>
<td>

<?php 
}else{?>


<td>
<select name="" class="select2 form-control">
        <option value="">UOM</option>
</select>       
</td>
<td>


<?php }*/

if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $sql = queryGet("SELECT * FROM `".ERP_INVENTORY_ITEMS."` WHERE `itemId`=$itemId");
    $sql_data = $sql['data'];
    $itemType = $sql_data['goodsType'];
    $buom_id = $sql_data['baseUnitMeasure'];
    $iuom_id = $sql_data['issueUnitMeasure'];
    $buom_sql = queryGet("SELECT * FROM `".ERP_INVENTORY_MASTER_UOM."` WHERE `uomId`=$buom_id");
    $buom = $buom_sql["data"]["uomName"];
    $iuom_sql = queryGet("SELECT * FROM `".ERP_INVENTORY_MASTER_UOM."` WHERE `uomId`=$iuom_id");
    $iuom = $iuom_sql["data"]["uomName"];




//$itemType='1'; //RM=1|| SFG=2|| FG=3

$uom='';
$uom.='
        <option value="">UOM</option>   
        <option value="'.$sql_data['baseUnitMeasure'].'" selected>'.$buom.'</option>
        <option value="'.$sql_data['issueUnitMeasure'].'">'.$iuom.'</option>';

$slocation='';

// if($itemType=='1')
// {
// $slocation.='
// <option value="">Select Storage Location</option>   
// <option value="rmWhOpen">RM Open</option>
// <option value="rmWhReserve">RM Reserve</option>
// <option value="rmProdReserve">RM Oroduction Reserve</option>
// <option value="rmProdOpen">RM Production Reserve</option>';
// }else if($itemType=='2')
// {
// $slocation.='
// <option value="">Select Storage Location</option>   
// <option value="sfgStockOpen">SFG Open</option>
// <option value="sfgStockReserve">SFG Reserve</option>
// ';
    
// }else if($itemType=='3' || $itemType=='4')
// {
// $slocation.='
// <option value="">Select Storage Location</option>   
// <option value="fgWhOpen">FG Open</option>
// <option value="fgWhReserve">FG Reserve</option>
// <option value="fgMktOpen">FG Market Open</option>
// <option value="fgMktReserve">FG Market Reserve</option>';
// }else
// {
    
// $slocation.='
// <option value="">Select Storage Location</option>';
// }
$slocation.='
<option value="">Select Storage Location</option>   
<option value="rmWhOpen">RM Open</option>


<option value="rmProdOpen">RM Production Open</option>
 <option value="sfgStockOpen">SFG Open</option>
 <option value="fgWhOpen">FG Open</option>
 <option value="fgMktOpen">FG Market Open</option>';


$mwp_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
 $mwp = $mwp_sql["data"]["movingWeightedPrice"];
$responseData['mwp']=$mwp;
$responseData['uom']=$uom;
$responseData['slocation']=$slocation;
// <option value="rmWhReserve">RM Reserve</option>
// <option value="rmProdReserve">RM Production Reserve</option>
// <option value="sfgStockReserve">SFG Reserve</option>
// <option value="fgWhReserve">FG Reserve</option>
// <option value="fgMktReserve">FG Market Reserve</option>



echo json_encode($responseData);
}


else if($_GET['act'] === "maxlimit"){
    $sl_type = $_GET['storagelocationId'];
    $itemId = $_GET['ItemId'];
     $sql = queryGet("SELECT ".$sl_type." as max FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
     echo $sql["data"]["max"];
     $responseData = $sql["data"]["max"];
     //$responseData['max']=$max;
//      echo $responseData['max'];
  

}
else{

}
