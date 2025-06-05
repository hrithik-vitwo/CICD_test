<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <?php if (isset($msg)) { ?>
            <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
                <?= $msg ?>
            </div>
        <?php } ?>
        <div class="container-fluid">
            <div class="row m-0 p-0 py-1 justify-content-between">
                <div class="ml-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Goods</a></li>
                        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']) . "?bom=" . $_GET["bom"]; ?>" class="text-dark">BOM</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- main content -->
    <section class="content">
        <!-- All massages, logics, consoles  -->
        <div class="row m-0 p-0 messages justify-content-end">
            <?php
            $goodsBomController = new GoodsBomController();
            $itemId = isset($_GET["bom"]) ? base64_decode($_GET["bom"]) : "";
            $goodsDetailsObj = $goodsController->getGoodsDeatils($itemId);
            $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
            $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
            $rmGoodsObj = $goodsController->getAllRMGoods();
            ?>
        </div>
        <!-- /.All massages, logics, consoles -->


        <?php
        if (isset($_POST["createBomSubmitBtn"])) {
            $createBomObj = $goodsBomController->createBom($_POST);
            if($createBomObj["status"] == "success"){
                swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"], LOCATION_URL . "manage-bom.php");
            }else{
                swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"]);
            }
            //console($createBomObj);
        } else {
            if (!$goodsBomController->isBomCreated($itemId)) {
            ?>
                    <!-- BOM Form -->
                    <div class="card p-0 bom-form-card">
                        <form action="" method="post" id="billOfMeterialForm">
                            <div class="card-header p-2 h5 text-muted bg-secondary">Bill Of Meterial Form</div>
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span>Prepared By</span>
                                        <input type="hidden" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminId"] ?? ""; ?>">
                                        <input type="hidden" name="itemId" value="<?= $itemId ?>">
                                        <input type="text" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <span>Prepared Date</span>
                                        <input type="date" value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <span>Good Title</span>
                                        <input type="text" value="<?= $goodTitle ?>" placeholder="Good title" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <span>Good Code</span>
                                        <input type="text" value="<?= $goodCode ?>" placeholder="Good Code" class="form-control" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div class="card">
                                    <div class="card-header p-1 pl-2 text-muted">BOM Items</div>
                                    <div class="card-body p-1">
                                        <div class="customTable border rounded">
                                            <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">BOM Items</small></div>
                                            <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                                <div class="border-right p-1 justify-content-between" style="width: 15%;">Item Title
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                                </div>
                                                <div class="border-right p-1 justify-content-between" style="width: 10%;">Item Code
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                                </div>
                                                <div class="border-right p-1 justify-content-between" style="width: 5%;">Type
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Consumption
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of items"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Extra(%)
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">UOM
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Rate
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One item rate(price)"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Amount
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 15%;">Remark
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                                </div>
                                                <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                            </div>
                                            <div class="goodItemsDiv">
                                                <div class="row m-0 border-top border-bottom justify-content-between" id="goodItemsDivRow_0">
                                                    <div class="border-right p-1" style="width: 15%;">
                                                        <input type="hidden" name="goodItemId[]" id="goodItemId_0" class="form-control m-0">
                                                        <input type="text" id="goodItemName_0" placeholder="Select Item" class="form-control m-0 dropdown-toggle goodItemInputBox" id="dropdownGoodItemInput_0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                                                        <div class="dropdown-menu itemListSuggestionDiv" id="dropdownGoodItem_0" aria-labelledby="dropdownGoodItemInput_0" style="max-height: 300px; overflow-y:scroll;">
                                                            <span class="dropdown-item btn dropdownGoodItem" data-id="0">Enter keyword for search...</span>
                                                        </div>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 10%;">
                                                        <span id="goodItemCode_0"></span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;">
                                                        <span id="goodItemType_0"></span>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="text" name="goodItemConsumption[]" id="goodItemConsumption_0" placeholder="Item Consumption" class="form-control m-0 goodItemConsumtion">
                                                    </div>
                                                    <div class="border-right p-1 d-flex" style="width: 10%;">
                                                        <input type="text" name="goodItemExtraPurchage[]" id="goodItemExtraPurchage_0" placeholder="Extra Purchase" class="form-control m-0 goodItemExtraPurchage"><span class="text-muted mt-1 ml-1">%</span>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="text" name="goodItemUOM[]" id="goodItemUOM_0" placeholder="Item UOM" class="form-control m-0 goodItemUOM" readonly>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="number" step="any" name="goodItemRate[]" id="goodItemRate_0" placeholder="Item Rate" class="form-control m-0 goodItemRate">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_0" placeholder="Item Amount" class="form-control m-0 goodItemAmount">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                        <textarea name="goodItemRemark[]" id="goodItemRemark_0" placeholder="Item Remark" rows="1" class="form-control m-0 p-2 goodItemRemark"></textarea>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-plus bg-success rounded p-1 addGoodItemsDivItemBtn" style="cursor: pointer;"></i></div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">BOM Activities</small></div>
                                            <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                                <div class="border-right p-1 justify-content-between" style="width: 15%;">Cost Center Name
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small>
                                                </div>
                                                <div class="border-right p-1 justify-content-between" style="width: 15%;">Cost Center Code
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Code"></i></small>
                                                </div>
                                                <div class="border-right p-1 justify-content-between" style="width: 10%;"><small>Consumption(Hr)</small>
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption in hour"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">LHR
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Labour hour rate"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">MHR
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Machine hour rate"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">LHR+MHR
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Labour and Machine hour rate"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Total Cost
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 15%;">Remark
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                                </div>
                                                <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                            </div>
                                            <div class="goodActivitiesDiv">
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1" style="width: 15%;">
                                                        <input type="hidden" name="goodActivityId[]">
                                                        <input type="text" id="goodActivityName_0" placeholder="Select Cost Center" class="form-control m-0 dropdown-toggle goodActivityInputBox" id="dropdownGoodActivityInput_0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                                                        <div class="dropdown-menu activityListSuggestionDiv" id="dropdownGoodActivity_0" aria-labelledby="dropdownGoodActivityInput_0" style="max-height: 300px; overflow-y:scroll;">
                                                            <span class="dropdown-item btn dropdownGoodActivity" data-id="0">Enter keyword for search...</span>
                                                        </div>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 15%;">
                                                        <span id="goodActivityCode_0"></span>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="number" step="any" name="goodActivityConsumption[]" id="goodActivityConsumption_0" placeholder="Activity Consumption" class="form-control m-0 goodActivityConsumption">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="text" name="goodActivityLhr[]" id="goodActivityLhr_0" placeholder="Activity LHR" class="form-control m-0 goodActivityLhr">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="text" name="goodActivityMhr[]" id="goodActivityMhr_0" placeholder="Activity MHR" class="form-control m-0 goodActivityMhr">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="text" name="goodActivityLhrMhr[]" id="goodActivityLhrMhr_0" placeholder="LHR+MHR" class="form-control m-0 goodActivityLhrMhr" readonly>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="number" step="any" name="goodActivityAmount[]" id="goodActivityAmount_0" placeholder="Total Amount" class="form-control m-0" readonly>
                                                    </div>
                                                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                        <textarea name="goodActivityRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"></textarea>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-plus bg-success rounded p-1 addGoodActivitiesDivItemBtn" style="cursor: pointer;"></i></div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">Others</small></div>
                                            <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                                <div class="border-right p-1 justify-content-between" style="width: 70%;">Other Details
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Other details"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 10%;">Amount
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                                                </div>
                                                <div class="border-right p-1" style="width: 15%;">Remark
                                                    <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                                </div>
                                                <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                            </div>
                                            <div class="goodOthersDiv">
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1" style="width: 70%;">
                                                        <input type="text" name="goodOthersItem[]" placeholder="Enter Other detail" class="form-control m-0">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 10%;">
                                                        <input type="number" step="any" name="goodOthersAmount[]" placeholder="Other Amount" class="form-control m-0">
                                                    </div>
                                                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                        <textarea name="goodOthersRemark[]" placeholder="Other Remark" rows="1" class="form-control m-0 p-2"></textarea>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-plus bg-success rounded p-1 addGoodOthersDivItemBtn" style="cursor: pointer;"></i></div>
                                                </div>
                                            </div>
                                            <!-- <div class="grandTotalDiv col-md-6 ml-auto lr-0 p-0 border-left">
                                                <div class="row m-0 border-bottom font-weight-bold text-light" style="background-color: #003060;">
                                                    <span class="pl-1">Grand Cost & Profit Details</span>
                                                </div>
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-end" style="width: 40%;">
                                                        <span class="font-weight-bold text-muted">TOTAL BLENDED COST</span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center" style="width: 60%;">
                                                        <span class="font-weight-bold text-muted">10000.00</span>
                                                    </div>
                                                </div>
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-end" style="width: 40%;">
                                                        <span class="font-weight-bold text-muted">Prifit(%)</span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center" style="width: 60%;">
                                                        <input type="number" step="any" placeholder="e.g 20" class="form-control m-0">
                                                    </div>
                                                </div>
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-end" style="width: 40%;">
                                                        <span class="font-weight-bold text-muted">MSP</span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center" style="width: 60%;">
                                                        <span class="font-weight-bold text-muted">10000.00</span>
                                                    </div>
                                                </div>
                                                <div class="row m-0 border-top border-bottom justify-content-between">
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-end" style="width: 40%;">
                                                        <span class="font-weight-bold text-muted">Max Discount(%)</span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center" style="width: 60%;">
                                                        <input type="number" step="any" placeholder="e.g 20" class="form-control m-0">
                                                    </div>
                                                </div>
                                                <div class="row m-0 border-top justify-content-between">
                                                    <div class="border-right p-1 row m-0 align-items-center justify-content-end" style="width: 40%;">
                                                        <span class="font-weight-bold text-muted">Discounted Price</span>
                                                    </div>
                                                    <div class="border-right p-1 row m-0 align-items-center" style="width: 60%;">
                                                        <span class="font-weight-bold text-muted">8000.00</span>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="card-footer row m-0 p-0">
                                        <input type="submit" value="Save" name="createBomSubmitBtn" class="btn btn-primary text-light form-control my-3" />
                                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-sm btn-danger">Back</button></a>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.BOM Form -->
            <?php
            } else {
                redirect(LOCATION_URL . "manage-bom.php?view=" . $_GET["bom"]);
            }
        }
        ?>
    </section>
    <!-- /.main content -->
    <script>
        function getTime() {
            return (new Date()).getTime();
        }

        $(document).ready(function() {
            $(".clickTheFormSaveBtn").click(function() {
                console.log("Clicked the save button");
                $("#billOfMeterialForm").submit();
            });
            console.log("Time: ", getTime());
            // calculation
            function calculateItemCost(rowNo = null) {
                let rate = $(`#goodItemRate_${rowNo}`).val();
                let qty = $(`#goodItemConsumption_${rowNo}`).val();
                let extra = $(`#goodItemExtraPurchage_${rowNo}`).val();
                let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
                let itemAmount = rate * totalQty;
                $(`#goodItemAmount_${rowNo}`).val(itemAmount);
            }

            function calculateActivityCost(rowNo = null) {
                console.log("Calculating activity cost");
                let lhrVal = parseFloat($(`#goodActivityLhr_${rowNo}`).val()) > 0 ? parseFloat($(`#goodActivityLhr_${rowNo}`).val()) : 0;
                let mhrVal = parseFloat($(`#goodActivityMhr_${rowNo}`).val()) > 0 ? parseFloat($(`#goodActivityMhr_${rowNo}`).val()) : 0;
                let lhrMhr = lhrVal+mhrVal;
                $(`#goodActivityLhrMhr_${rowNo}`).val(lhrMhr);
                let activityConsumption = $(`#goodActivityConsumption_${rowNo}`).val();
                let activityCost = lhrMhr * activityConsumption;
                $(`#goodActivityAmount_${rowNo}`).val(activityCost);
            }

            $(document).on("keyup", ".goodItemConsumtion", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateItemCost(rowNo);
            });
            $(document).on("keyup", ".goodItemExtraPurchage", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateItemCost(rowNo);
            });
            $(document).on("keyup", ".goodItemRate", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateItemCost(rowNo);
            });

            $(document).on("keyup", ".goodActivityConsumption", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateActivityCost(rowNo);
            });
            $(document).on("keyup", ".goodActivityLhr", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateActivityCost(rowNo);
            });
            $(document).on("keyup", ".goodActivityMhr", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateActivityCost(rowNo);
            });



            $(document).on("keyup", ".goodItemInputBox", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                let keyWord = $(this).val();
                //console.log("change item", $(this).val());
                $.ajax({
                    type: "GET",
                    url: `<?= LOCATION_URL ?>ajaxs/goods/bom/ajax-goods-item-suggestion-names.php?keyWord=${keyWord}`,
                    beforeSend: function() {
                        $("#dropdownGoodItem_1").html(`<span class="dropdown-item btn dropdownGoodItem" data-id="0">Loding...</span>`);
                    },
                    success: function(response) {
                        $(`#dropdownGoodItem_${rowNo}`).html(response);
                    }
                });
            });
            
            $(document).on("click", ".dropdownGoodItem", function() {
                let rowNo = ($(this).parent().attr("id")).split("_")[1];
                let goodId = $(this).data("id");
                console.log("rowNo", rowNo);
                if (goodId > 0) {
                    $.ajax({
                        type: "GET",
                        url: `<?= LOCATION_URL ?>ajaxs/goods/bom/ajax-goods-item-details.php?itemId=${goodId}`,
                        success: function(response) {
                            let goodDataObj = JSON.parse(response);
                            if (goodDataObj["status"] == "success") {
                                console.log(goodDataObj);
                                let goodData = goodDataObj["data"];
                                $(`#goodItemId_${rowNo}`).val(goodData["itemId"]);
                                $(`#goodItemName_${rowNo}`).val(goodData["itemName"]);
                                $(`#goodItemCode_${rowNo}`).html(goodData["itemCode"]);
                                $(`#goodItemType_${rowNo}`).html(goodData["type"]);
                                $(`#goodItemConsumption_${rowNo}`).val(1);
                                $(`#goodItemExtraPurchage_${rowNo}`).val(0);
                                $(`#goodItemUOM_${rowNo}`).val(goodData["uomName"]);
                                $(`#goodItemRate_${rowNo}`).val(goodData["movingWeightedPrice"]);
                                $(`#goodItemAmount_${rowNo}`).val(goodData["movingWeightedPrice"]);
                            } else {
                                console.log(goodDataObj);
                            }
                        }
                    });
                } else {
                    console.log(goodId);
                }
            });

            $(document).on("keyup", ".goodActivityInputBox", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                let keyWord = $(this).val();
                //console.log("change item", $(this).val());
                $.ajax({
                    type: "GET",
                    url: `<?= LOCATION_URL ?>ajaxs/goods/bom/ajax-goods-item-suggestion-activities.php?keyWord=${keyWord}`,
                    beforeSend: function() {
                        $(`#dropdownGoodActivity_${rowNo}`).html(`<span class="dropdown-item btn dropdownGoodActivity" data-id="0">Loding...</span>`);
                    },
                    success: function(response) {
                        $(`#dropdownGoodActivity_${rowNo}`).html(response);
                    }
                });
            });

            $(document).on("click", ".dropdownGoodActivity", function() {
                let rowNo = ($(this).parent().attr("id")).split("_")[1];
                console.log("dropdownGoodActivity rowNo", rowNo);
                let lhr = $(this).data("lhr");
                let mhr = $(this).data("mhr");
                let costCenterCode = $(this).data("costcentercode");
                let costCenterTitle = $(this).data("title");

                $(`#goodActivityName_${rowNo}`).val(costCenterTitle);
                $(`#goodActivityConsumption_${rowNo}`).val(1);
                $(`#goodActivityCode_${rowNo}`).html(costCenterCode);
                $(`#goodActivityLhr_${rowNo}`).val(lhr);
                $(`#goodActivityMhr_${rowNo}`).val(mhr);
                $(`#goodActivityLhrMhr_${rowNo}`).val(lhr + mhr);
                $(`#goodActivityAmount_${rowNo}`).val(lhr + mhr);

                console.log("lhr, mhr", lhr, mhr, costCenterCode, costCenterTitle);
            });



            $(document).on("click", ".addGoodItemsDivItemBtn", function() {
                let rowNo = getTime();
                $(".goodItemsDiv").append(`
                    <div class="row m-0 border-top border-bottom justify-content-between" id="goodItemsDivRow_${rowNo}">
                        <div class="border-right p-1" style="width: 15%;">
                            <input type="hidden" name="goodItemId[]" id="goodItemId_${rowNo}" placeholder="Select Item" class="form-control m-0">
                            <input type="text" id="goodItemName_${rowNo}" placeholder="Select Item" class="form-control m-0 dropdown-toggle goodItemInputBox" id="dropdownGoodItemInput_0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                            <div class="dropdown-menu itemListSuggestionDiv" id="dropdownGoodItem_${rowNo}" aria-labelledby="dropdownGoodItemInput_${rowNo}" style="max-height: 300px; overflow-y:scroll;">
                            <span class="dropdown-item btn dropdownGoodItem" data-id="0">Enter keyword for search...</span>
                            </div>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 10%;">
                                <span id="goodItemCode_${rowNo}"></span>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;">
                            <span id="goodItemType_${rowNo}"></span>
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodItemConsumption[]" id="goodItemConsumption_${rowNo}" placeholder="Item Consumtion" class="form-control m-0 goodItemConsumtion">
                        </div>
                        <div class="border-right p-1 d-flex" style="width: 10%;">
                            <input type="text" name="goodItemExtraPurchage[]" id="goodItemExtraPurchage_${rowNo}" placeholder="Extra Purchase" class="form-control m-0 goodItemExtraPurchage"><span class="text-muted mt-1 ml-1">%</span>
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodItemUOM[]" id="goodItemUOM_${rowNo}" placeholder="Item UOM" class="form-control m-0 goodItemUOM" readonly>
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodItemRate[]" id="goodItemRate_${rowNo}" placeholder="Item Rate" class="form-control m-0 goodItemRate">
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodItemAmount[]" id="goodItemAmount_${rowNo}" placeholder="Item Amount" class="form-control m-0">
                        </div>
                        <div class="border-right p-1" style="width: 15%; height: 100%;">
                            <textarea name="goodItemRemark[]" id="goodItemRemark_${rowNo}" placeholder="Item Remark" rows="1" class="form-control m-0 p-2"></textarea>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-minus bg-danger rounded p-1 removeGoodItemsDivItemBtn" style="cursor: pointer;"></i></div>
                    </div>`);
            });

            $(document).on("click", ".addGoodActivitiesDivItemBtn", function() {
                let rowNo = getTime();
                $(".goodActivitiesDiv").append(`
                    <div class="row m-0 border-top border-bottom justify-content-between">
                        <div class="border-right p-1" style="width: 15%;">
                            <input type="hidden" name="goodActivityId[]">
                            <input type="text" id="goodActivityName_${rowNo}" placeholder="Select Cost Center" class="form-control m-0 dropdown-toggle goodActivityInputBox" id="dropdownGoodActivityInput_0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" autocomplete="off">
                            <div class="dropdown-menu activityListSuggestionDiv" id="dropdownGoodActivity_${rowNo}" aria-labelledby="dropdownGoodActivityInput_${rowNo}" style="max-height: 300px; overflow-y:scroll;">
                            <span class="dropdown-item btn dropdownGoodActivity" data-id="0">Enter keyword for search...</span>
                            </div>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 15%;">
                            <span id="goodActivityCode_${rowNo}"></span>
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodActivityConsumption[]" id="goodActivityConsumption_${rowNo}" placeholder="Activity Consumption" class="form-control m-0 goodActivityConsumption">
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodActivityLhr[]" id="goodActivityLhr_${rowNo}" placeholder="Activity LHR" class="form-control m-0 goodActivityLhr">
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodActivityMhr[]" id="goodActivityMhr_${rowNo}" placeholder="Activity MHR" class="form-control m-0 goodActivityMhr">
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="text" name="goodActivityLhrMhr[]" id="goodActivityLhrMhr_${rowNo}" placeholder="LHR+MHR" class="form-control m-0 goodActivityLhrMhr" readonly>
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="number" step="any" name="goodActivityAmount[]" id="goodActivityAmount_${rowNo}" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
                        </div>
                        <div class="border-right p-1" style="width: 15%; height: 100%;">
                            <textarea name="goodActivityRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"></textarea>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-minus bg-danger rounded p-1 removeGoodActivitiesDivItemBtn" style="cursor: pointer;"></i></div>
                    </div>`);
            });
            $(document).on("click", ".addGoodOthersDivItemBtn", function() {
                let rowNo = getTime();
                $(".goodOthersDiv").append(`
                    <div class="row m-0 border-top border-bottom justify-content-between">
                        <div class="border-right p-1" style="width: 70%;">
                            <input type="text" name="goodOthersItem[]" id="goodOthersItem_${rowNo}" placeholder="Enter Other Details" class="form-control m-0">
                        </div>
                        <div class="border-right p-1" style="width: 10%;">
                            <input type="number" step="any" name="goodOthersAmount[]" id="goodOthersAmount_${rowNo}" placeholder="Other Amount" class="form-control m-0">
                        </div>
                        <div class="border-right p-1" style="width: 15%; height: 100%;">
                            <textarea name="goodOthersRemark[]" placeholder="Other Remark" rows="1" class="form-control m-0 p-2"></textarea>
                        </div>
                        <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;"><i class="fa fa-minus bg-danger rounded p-1 removeGoodOthersDivItemBtn" style="cursor: pointer;"></i></div>
                    </div>`);
            });

            $(document).on("click", ".removeGoodItemsDivItemBtn", function() {
                let elm = $(this).parent().parent().remove();
            });
            $(document).on("click", ".removeGoodActivitiesDivItemBtn", function() {
                let elm = $(this).parent().parent().remove();
            });
            $(document).on("click", ".removeGoodOthersDivItemBtn", function() {
                let elm = $(this).parent().parent().remove();
            });

        });
    </script>
</div>