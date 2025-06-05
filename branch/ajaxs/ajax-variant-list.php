<?php
include_once("../../app/v1/connection-branch-admin.php");
//include("../../../../app/v1/functions/branch/func-items-controller.php");
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

if ($_GET['act'] === "variant") {
    $yearId = $_GET['yearId'];
    $admin_month_var = $_GET['admin_month_var'];
    $sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `year_id`=$yearId ",true);
    $sql_data = $sql['data'];
   // console($sql_data);
    foreach($sql_data as $data){
       ?>
          
       
        <option value="<?= $data['month_variant_id'] ?>" <?= ($data['month_variant_id'] == $admin_month_var) ? "selected" : ""  ?> ><?= $data['month_variant_name'] ?></option>
        <?php
       

    }
   










// $responseData['month']=$month;


echo json_encode($responseData);
}

else{

}
