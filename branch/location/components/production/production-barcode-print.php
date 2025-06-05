<?php
       
  require_once("../../../../app/v1/connection-branch-admin.php"); 
 

//   require_once("../../../common/header.php");
//  require_once("../../../common/navbar.php");
//  require_once("../../../common/sidebar.php");
//  require_once("../../../common/pagination.php");
 require_once("../../../../app/v1/functions/company/func-branches.php");



                
  require_once("../../bom/controller/bom.controller.php");
              
$post = $_GET; 
                 
     function validateConsumption($itemId, $itemQty, $declearDate)
    {
        $bomControllerObj = new BomController();
        // $bomDetailObj = $bomControllerObj->getBomDetails($itemId);
        $bomDetailObj = $bomControllerObj->getBomDetailsByItemId($itemId);
        $isStockAvailable = true;
        foreach ($bomDetailObj["data"]["bom_material_data"] as $bomOneItem) {
            $totalRequiredConsumption = $bomOneItem["totalConsumption"] * $itemQty;
            $stockLogObj = itemQtyStockChecking($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"),null, $declearDate);
            $itemAvailableStocks = $stockLogObj['sumOfBatches'];
            if ($itemAvailableStocks < $totalRequiredConsumption) {
                $isStockAvailable = false;
            }
        }
        return $isStockAvailable;
    }
                      

 function previewConsumption($posts)
{
   // console($post);
    // First check if it's a base64 encoded string.
$post = json_decode(base64_decode($posts['data']), true);
// console($post);


    global $company_id;
    global $branch_id;
    global $location_id;
   
    
    $dbObj = new Database();
    $declearItemId = $post["itemId"] ?? 0;
    $declearItemCode = $post["itemCode"] ?? "";
    $declearItemQty = $post["productionQuantity"] ?? 0;
    $declearDate =$post["productionDeclareDate"];

   // $isStockAvailable = validateConsumption($declearItemId, $declearItemQty,$declearDate);
    
      
        $getLastProdDeclareSlNoObj = $dbObj->queryGet('SELECT `itemSlno` FROM `erp_inventory_stocks_fg_barcodes` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $declearItemId . ' ORDER BY `itemSlno` DESC LIMIT 1');
      //  console($getLastProdDeclareSlNoObj);
        if ($getLastProdDeclareSlNoObj["status"] == "success" && $getLastProdDeclareSlNoObj["data"]["itemSlno"] > 0) {
            $lastDeclearQtySl = ($getLastProdDeclareSlNoObj["data"]["itemSlno"] ?? 0) + 1;
        } else {
            $lastDeclearQtySl = 1;
        }
        $uniqueBarCodes = [];
        for ($declearQtySl = $lastDeclearQtySl; $declearQtySl < $lastDeclearQtySl + $declearItemQty; $declearQtySl++) {
            $lotNumber = date("YmdHms");
            $uniqueBarCodes[] = ["declearItemCode" => $declearItemCode, "lotNumber" => $lotNumber, "declearQtySl" => $declearQtySl, "barcode" => $declearItemCode . "/" . $lotNumber . "/" . $declearQtySl];
        }
        return [
            "status" => "success",
            "message" => "Successfully generated QR Codes",
            "data" => $uniqueBarCodes
        ];
    
}

$previewObj  = previewConsumption($post);
// console($previewObj);
// exit();

?>

<link rel="stylesheet" href="../../../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>


<style>
    .bar-code-title.d-flex {
        font-family: cursive;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
    }

    .bar-code-title.d-flex p {
        font-family: cursive;
        color: #fff;
    }

    .card.bar-code-multiple-card {
    flex: 1 1 200px;
    max-width: 200px;
    min-width: 200px;
    width: 100%;
    box-shadow: 6px 7px 12px -3px #00000052;
    border-radius: 5px;
}

    .card.bar-code-multiple-card .card-footer {
        background-color: #003060;
    }

    svg.bar-code-img {
        max-width: 300px;
        width: 100%;
        height: auto;
        display: block;
    }

    .row.bar-code-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-around;
}
    .bar-code-btns {
        gap: 7px;
    }

    @media (max-width: 768px) {
    .card.bar-code-multiple-card {
        flex: 1 1 100%; /* Stack cards on smaller screens */
    }

    @media print {
    body {
        visibility: visible;  /* Ensure everything is visible */
    }
    /* Additional styles if needed */
}


    /* Or any other specific styles that hide content during printing */
}
</style>
<div class="content-wrapper d-flex" id = "printableDiv">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Print Bar Codes</h3>
                               
                                
                            </li>
                        </ul>
                    </div>

                  
                
                  
                            
                                
                                <div class="card" style="border-radius: 20px;">
                                <div class="row bar-code-cards p-0 m-0">
    <?php
    foreach ($previewObj["data"] as $oneItem) {
    ?>
        <div class="card bar-code-multiple-card m-2 d-flex">
            <div class="card-body p-0">
                <svg class="bar-code-img" id="barcode<?= $oneItem["declearQtySl"] ?>"></svg>
            </div>
            <div class="card-footer">
                <div class="bar-code-title d-flex">
                    <p>Mfg</p>
                    <p><?= date("d-m-Y") ?></p>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                JsBarcode("#barcode<?= $oneItem["declearQtySl"] ?>", "<?= $oneItem["barcode"] ?>", {
                    fontSize: 14,
                    fontOptions: "bold",
                    margin: 5,
                    height: 75,
                    width: 1
                });
            });
        </script>
    <?php
    }
    ?>
</div>

                                </div>
                          
                 
                </div>
    </section>
</div>
<script>
 $(document).ready(function() {
    setTimeout(function() {
        window.print();
    }, 500);  // Delay of 500ms, adjust as needed
});


</script>

<?php
    require_once("../common/footer2.php");
?>