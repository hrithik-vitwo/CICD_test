<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/boq/boq.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOQ</a></li>
    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Bill of Quantities</a></li>
    <li class="back-button">
        <a href="<?= BASE_URL ?>branch/location/boq/boq.php">
            <i class="fa fa-reply po-list-icon"></i>
        </a>
    </li>
</ol>

<!-- All massages, logics, consoles  -->
<div class="row m-0 p-0 messages justify-content-end">
    <?php
    $itemId = base64_decode($_GET["create"]);
    $goodsDetailsObj = $goodsController->getGoodsDeatils($itemId);
    $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
    $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
    $rmGoodsObj = $goodsController->getAllRMGoods();
    $expenseGlListObj = getAllChartOfAccounts_list_by_p($company_id, 4);
    ?>
</div>
<!-- /.All massages, logics, consoles -->
<?php
if (isset($_POST["createBoqSubmitBtn"])) {
    // console($_POST);
    $createboqObj = $boqControllerObj->createBoq($_POST);
    if ($createboqObj["status"] == "success") {
        swalAlert($createboqObj["status"], ucfirst($createboqObj["status"]), $createboqObj["message"], LOCATION_URL . "boq/boq.php");
    } else {
        swalAlert($createboqObj["status"], ucfirst($createboqObj["status"]), $createboqObj["message"]);
    }
    // console($createboqObj);
}
?>
<style>
    .content-wrapper {
        height: auto !important;
    }

    .select2-container {
        display: inline-block;
        max-width: 200px;
        width: 100% !important;
        box-sizing: border-box;
        margin: 0;
        position: relative;
        vertical-align: middle;
    }

    .bill-qty-section input {
        background: transparent !important;
    }

    .boq-section .display-flex-space-between {
        margin: 5px 0;
    }


    .boq-section table th,
    .boq-section table td {
        font-size: 10px !important;
        padding: 7px 6px !important;
        border: 0 !important;
    }

    .boq-section table th {
        background: #cdd6df !important;
        color: #022c56 !important;
        font-weight: 600 !important;

    }

    .boq-section table td {
        background: transparent !important;
    }

    .boq-section table td input,
    .boq-section table td .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 10px !important;
        padding: 7px 15px !important;
        line-height: 23px;
    }

    .card.activity-dotted-card {
        border: 1px solid #ccc;
    }

    .card.activity-dotted-card label {
        position: absolute;
        background: #f7f8f9;
        padding: 5px;
        top: -17px;
        z-index: 0;
    }

    .bill-qty-section h2.title.d-flex input {
        width: 20%;
        padding: 5px;
    }

    .bill-qty-section h2.title {
        background: #cdd6df;
        padding: 5px 10px;
    }


    @media (max-width: 768px) {

        .card.p-0.boq-form-card.bg-transparent.boq-section .card-body {
            padding: 0 !important;
        }

        .bill-qty-section h2.title.d-flex input {
            width: 40%;
        }

        .bill-qty-section {
            margin-bottom: 2em;
        }

        .scrollable-tab {
            overflow: auto;
        }

        .scrollable-tab::-webkit-scrollbar {
            height: 2px;
        }
    }
</style>
<div class="container-fluid">
    <div class="card p-0 boq-form-card bg-transparent boq-section">
        <div class="card-body">
            <form action="" method="post">
                <div class="row my-4">
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <div class="bill-qty-section">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h2 class="title d-flex">
                                        <input type="text" name="itemTitle" value="<?= $goodTitle ?>" placeholder="Service title" class="form-control border-0 text-sm font-bold mb-0" readonly>
                                        <p class="text-sm text-normal">||</p>
                                        <input type="text" name="itemCode" value="<?= $goodCode ?>" placeholder="Service Code" class="form-control border-0 text-sm d-inline-block" readonly>
                                    </h2>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="display-flex-space-between text-nowrap gap-2">
                                        <p class="text-xs">Prepared By</p>
                                        <input type="hidden" name="itemId" value="<?= $itemId ?>">
                                        <input type="text" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control border-0 font-bold" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="display-flex-space-between text-nowrap gap-2">
                                        <p class="text-xs">at</p>
                                        <input type="date" value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control border-0 font-bold" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7">
                        <div class="acc-summary">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h2 class="text-sm font-bold mb-0">Account Summary</h2>
                                    <hr class="my-2">
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="display-flex-space-between">
                                        <p class="text-xs">Total Service Cost</p>
                                        <p class="text-xs" id="grandServiceCost">Rs 0.00</p>
                                        <input type="hidden" name="grandServiceCost" value="0.00" id="grandServiceCostInput">
                                    </div>
                                    <div class="display-flex-space-between">
                                        <p class="text-xs">Total Material Cost</p>
                                        <p class="text-xs" id="grandMaterialCost">Rs 0.00</p>
                                        <input type="hidden" name="grandMaterialCost" value="0.00" id="grandMaterialCostInput">
                                    </div>
                                    <div class="display-flex-space-between">
                                        <p class="text-xs">Total Activivty Cost</p>
                                        <p class="text-xs" id="grandActivityCost">Rs 0.00</p>
                                        <input type="hidden" name="grandActivityCost" value="0.00" id="grandActivityCostInput">
                                    </div>
                                    <hr class="mt-2 mb-2">
                                    <div class="display-flex-space-between">
                                        <p class="text-xs font-bold">Total Cost</p>
                                        <p class="text-xs font-bold" id="grandTotalBoqCost">Rs 0.00</p>
                                        <input type="hidden" name="grandTotalBoqCost" value="0.00" id="grandTotalBoqCostInput">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row my-4">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h5 class="text-sm font-bold">Services</h5>
                        <hr class="my-2">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="scrollable-tab">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Service Title <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Service details"></i></small></th>
                                        <th>Service Code <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Service details"></i></small></th>
                                        <th>Type <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Service details"></i></small></th>
                                        <th>UOM <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small></th>
                                        <th>Consumption <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of Services"></i></small></th>
                                        <th>Extra(%) <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small></th>
                                        <th>Rate <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One Service rate(price)"></i></small></th>
                                        <th>Amount <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                        <th>Remarks <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                        <th class="text-center"><i class="fa fa-plus btn-primary rounded p-2 addBoqServicesDivItemBtn" style="cursor: pointer;"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="boqServiceDiv"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row my-4">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h5 class="text-sm font-bold">Materials</h5>
                        <hr class="my-2">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="scrollable-tab">
                            <table>
                                <thead>
                                    <th>Item Title <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                    <th>Item Code <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                    <th>Type <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                    <th>UOM <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small></th>
                                    <th>Consumption <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of Items"></i></small></th>
                                    <th>Extra(%) <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small></th>
                                    <th>Rate <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One Item rate(price)"></i></small></th>
                                    <th>Amount <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                    <th>Remarks <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                    <th class="text-center"><i class="fa fa-plus btn-primary rounded p-2 addBoqMaterialsDivItemBtn" style="cursor: pointer;"></i></th>
                                </thead>
                                <tbody id="boqMaterialDiv">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card bg-transparent activity-dotted-card my-4">
                    <div class="card-body">
                        <label class="text-sm font-bold" for="">Activity</label>
                        <div class="row my-3">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-xs">Hourly Deployment</h5>
                                <hr class="my-2">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="scrollable-tab">
                                    <table>
                                        <thead>
                                            <th>Cost Center<small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small></th>
                                            <th>Code <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost center code"></i></small></th>
                                            <th>Hourly Deployment Type <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Hourly deployment type(LHR or MHR)"></i></small></th>
                                            <th>UOM <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                            <th>Consumption <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                            <th>Extra(%) <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                            <th>Rate <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                            <th>Amount <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                            <th>Remarks <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                            <th class="text-center"><i class="fa fa-plus btn-primary rounded p-2 addBoqHdDivItemBtn" style="cursor: pointer;"></i></th>
                                        </thead>
                                        <tbody id="boqHourlyDeploymentDiv">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row my-3">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-xs font-bold">Other Head</h5>
                                <hr class="my-2">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="scrollable-tab">
                                    <table>
                                        <thead>
                                            <th>Cost Center<small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small></th>
                                            <th>Code <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost center code"></i></small></th>
                                            <th>Other Head <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Other Head deatils"></i></small></th>
                                            <th>UOM <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                            <th>Consumption <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                            <th>Extra(%) <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                            <th>Rate <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                            <th>Amount <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                            <th>Remarks <small class="bg-dark px-1 rounded ml-2"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                            <th class="text-center"><i class="fa fa-plus btn-primary rounded p-2 addBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i></th>
                                        </thead>
                                        <tbody id="boqOtherHeadDiv">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 m-0 p-0 text-right">
                    <button type="submit" value="Save" name="createBoqSubmitBtn" class="btn btn-primary text-light my-3">Save BOQ</button>
                    <a class="btn btn-danger" href="<?= basename($_SERVER['PHP_SELF']); ?>">Back</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Other Head Modal -->
    <div class="modal fade" id="addBomOtherHeadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Activity Other Head</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="p-0 m-0" id="frmAddOtherExpenseHeadForm">
                    <div class="modal-body">
                        <input type="hidden" name="frmOtherHeadType" value="3">
                        <label for="">Head Code</label>
                        <input type="text" name="frmOtherHeadCode" placeholder="Code" class="form-control" required>
                        <label for="">Name</label>
                        <input type="text" name="frmOtherHeadName" placeholder="Name" class="form-control" required>
                        <!-- <label for="">Connect Gl <small>(Optional, it will use for analytics)</small></label>
                        <input type="text" name="frmOtherHeadGl" placeholder="Head Gl" class="form-control"> -->
                        <label for="">Connect Gl <small>(Optional, it will use for analytics)</small></label><br>
                        <select name="frmOtherHeadGl" class="form-control" id="frmOtherHeadGlDropDown" style="width:100%">
                            <option value="">Select Gl</option>
                            <?php
                            foreach ($expenseGlListObj["data"] as $row) {
                            ?>
                                <option value="<?= $row["id"] ?>"><?= $row["gl_label"] ?> - <?= $row["gl_code"] ?></option>
                            <?php
                            }
                            ?>
                        </select>

                        <div class="d-flex gap-2">
                            <div class="form-input" style="width:48%">
                                <label for="">Rate</label>
                                <input type="number" step="any" name="frmOtherHeadRate" placeholder="Rate" class="form-control" required>
                            </div>
                            <div class="form-input">
                                <label for=""></label>
                                <p>/</p>
                            </div>
                            <div class="form-input" style="width:48%">
                                <label for="">UOM</label>
                                <select name="frmOtherHeadUom" id="frmOtherHeadUomDropDown" class="form-control" required>
                                    <option value="">Select UOM</option>
                                    <?php
                                    $getUomListObj = getUomList('material');
                                    foreach ($getUomListObj["data"] as $row) {
                                    ?>
                                        <option value="<?= $row["uomName"] ?>"><?= $row["uomDesc"] ?> (<?= $row["uomName"] ?>)</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END New Other Head Modal -->
</div>

<script>
    $(document).ready(function() {
        $(`#frmOtherHeadGlDropDown`).select2({
            dropdownParent: $(`#addBomOtherHeadModal`)
        });
        $(`#frmOtherHeadUomDropDown`).select2({
            dropdownParent: $(`#addBomOtherHeadModal`)
        });
        //=============================================== [UPDATE GRAND COST] =======================================================
        function updateGrandTotalCost() {
            let grandMaterialCost = 0;
            $(".boqMaterialAmount").each(function() {
                grandMaterialCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })
            $("#grandMaterialCost").html(grandMaterialCost.toFixed(2));
            $("#grandMaterialCostInput").val(grandMaterialCost.toFixed(2));

            let grandServiceCost = 0;
            $(".boqServiceAmount").each(function() {
                grandServiceCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })
            $("#grandServiceCost").html(grandServiceCost.toFixed(2));
            $("#grandServiceCostInput").val(grandServiceCost.toFixed(2));

            let grandActivityCost = 0;
            $(".boqHdAmount").each(function() {
                grandActivityCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })
            $(".boqOtherHeadAmount").each(function() {
                grandActivityCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })
            $("#grandActivityCost").html(grandActivityCost.toFixed(2));
            $("#grandActivityCostInput").val(grandActivityCost.toFixed(2));

            let gradBoqCostAmount = (grandMaterialCost + grandServiceCost + grandActivityCost);
            $("#grandTotalBoqCost").html(gradBoqCostAmount.toFixed(2));
            $("#grandTotalBoqCostInput").val(gradBoqCostAmount.toFixed(2));
        }
        //============================================= [END UPDATE GRAND COST] =====================================================



        //================================================================ [START SERVICES] =========================================================
        function addBoqItemServiceNewRow(rowNo = 0) {
            $("#boqServiceDiv").append(`
                                    <tr id="boqServicesDivRow_${rowNo}">
                                        <td class="p-1">
                                            <input type="hidden" name="boqService[${rowNo}][ItemGl]" id="boqServiceGl_${rowNo}" value="0" class="form-control">
                                            <select name="boqService[${rowNo}][ItemId]" id="boqServiceId_${rowNo}" class="form-control serviceItemsDropDown" required>
                                                <option value="" data-row=""> -- Select Item -- </option>
                                                <?php
                                                // foreach (getGoodAndServiceItems(["SERVICES", "SERVICEP"])["data"] as $key => $itemObj) {
                                                foreach (getGoodAndServiceItems(["SERVICEP"])["data"] as $key => $itemObj) {
                                                    if ($itemObj["itemId"] == $itemId) continue;
                                                    echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqService[${rowNo}][Code]" id="boqServiceCode_${rowNo}" placeholder="Item Code" class="form-control m-0 boqServiceCode" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqService[${rowNo}][Type]" id="boqServiceType_${rowNo}" placeholder="Item Type" class="form-control m-0 boqServiceType" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqService[${rowNo}][Uom]" id="boqServiceUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 boqServiceUom" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="0.01" name="boqService[${rowNo}][Consumption]" id="boqServiceConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 boqServiceConsumtion boqServiceRowInput text-right">
                                        </td>
                                        <td class="p-1 d-flex">
                                            <input type="number" step="0.01" name="boqService[${rowNo}][ExtraPurchage]" id="boqServiceExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 boqServiceExtraPurchage boqServiceRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="0.01" name="boqService[${rowNo}][Rate]" id="boqServiceRate_${rowNo}" placeholder="0.00" class="form-control m-0 boqServiceRowInput boqServiceRate text-right">
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="0.01" name="boqService[${rowNo}][Amount]" id="boqServiceAmount_${rowNo}" placeholder="0.00" class="form-control m-0 boqServiceAmount text-right" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="text" name="boqService[${rowNo}][Remark]" id="boqServiceRemark_${rowNo}" placeholder="Item Remark" class="form-control m-0 boqServiceRemark">
                                        </td>
                                        <td class="p-1 text-center"><i class="fa fa-minus bg-danger rounded p-2 removeBoqServicesDivItemBtn" style="cursor: pointer;"></i></td>
                                    </tr> `);

            $(`#boqServiceId_${rowNo}`).select2();
        }
        $(document).on("change", ".serviceItemsDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            // console.log(selectVal);
            // console.log(rowData);
            console.log(rowDataObj);
            $(`#boqServiceGl_${rowNo}`).val(rowDataObj["parentGlId"]);
            $(`#boqServiceCode_${rowNo}`).val(rowDataObj["itemCode"]);
            $(`#boqServiceType_${rowNo}`).val(rowDataObj["type"]);
            $(`#boqServiceConsumption_${rowNo}`).val(1);
            $(`#boqServiceExtraPurchage_${rowNo}`).val(0);
            $(`#boqServiceUom_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#boqServiceRate_${rowNo}`).val(rowDataObj["itemboqPrice"]);
                $(`#boqServiceAmount_${rowNo}`).val(rowDataObj["itemboqPrice"]);
            } else {
                $(`#boqServiceRate_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
                $(`#boqServiceAmount_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
            }
            updateGrandTotalCost();
        });

        function calculateboqServiceOneRowCost(rowNo = null) {
            let rate = $(`#boqServiceRate_${rowNo}`).val();
            let qty = $(`#boqServiceConsumption_${rowNo}`).val();
            let extra = $(`#boqServiceExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#boqServiceAmount_${rowNo}`).val(itemAmount);
            updateGrandTotalCost();
        }

        $(document).on("keyup", ".boqServiceRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateboqServiceOneRowCost(rowNo);
        });

        var boqServiceRowNo = 0;
        addBoqItemServiceNewRow();
        $(document).on("click", ".addBoqServicesDivItemBtn", function() {
            console.log("addboqServicesDivItemBtn");
            addBoqItemServiceNewRow(boqServiceRowNo += 1);
            updateGrandTotalCost();
        });

        $(document).on("click", ".removeBoqServicesDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //================================================================= [END SERVICES] ==========================================================


        //================================================= [START MATERIAL] ==========================================================
        function addBoqItemMaterialNewRow(rowNo = 0) {
            $("#boqMaterialDiv").append(`<tr id="boqMaterialsDivRow_${rowNo}">
                                    <td class="p-1">
                                        <input type="hidden" name="boqMaterial[${rowNo}][ItemGl]" id="boqMaterialGl_${rowNo}" value="0" class="form-control">
                                        <select name="boqMaterial[${rowNo}][ItemId]" id="boqMaterialId_${rowNo}" class="form-control rmSfgItemsDropDown" required>
                                            <option value="" data-row=""> --Select Item-- </option>
                                            <?php
                                            foreach (getGoodAndServiceItems(["RM", "SFG"])["data"] as $key => $itemObj) {
                                                if ($itemObj["itemId"] == $itemId) continue;
                                                echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "boqMaterial[${rowNo}][Code]" id = "boqMaterialCode_${rowNo}" placeholder = "Item Code" class="form-control m-0 boqMaterialCode" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "boqMaterial[${rowNo}][Type]" id = "boqMaterialType_${rowNo}" placeholder = "Item Type" class="form-control m-0 boqMaterialType" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "boqMaterial[${rowNo}][Uom]" id = "boqMaterialUom_${rowNo}" placeholder = "Item UOM" class="form-control m-0 boqMaterialUom" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "number" step = "0.01" name = "boqMaterial[${rowNo}][Consumption]" id = "boqMaterialConsumption_${rowNo}" placeholder = "0.00" class = "form-control m-0 boqMaterialConsumtion boqMaterialRowInput text-right" >
                                    </td>
                                    <td class="p-1 d-flex">
                                        <input type = "number" step = "0.01" name = "boqMaterial[${rowNo}][ExtraPurchage]" id = "boqMaterialExtraPurchage_${rowNo}" placeholder = "0.00" class = "form-control m-0 boqMaterialExtraPurchage boqMaterialRowInput text-right" ><span class = "text-muted mt-1 ml-1">%</span>
                                    </td>
                                    <td class="p-1">
                                        <input type = "number" step = "0.01" name = "boqMaterial[${rowNo}][Rate]" id = "boqMaterialRate_${rowNo}" placeholder = "0.00" class = "form-control m-0 boqMaterialRate text-right" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "number" step = "0.01" name = "boqMaterial[${rowNo}][Amount]" id = "boqMaterialAmount_${rowNo}" placeholder = "0.00" class = "form-control m-0 boqMaterialAmount text-right" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type="text" name="boqMaterial[${rowNo}][Remark]" id="boqMaterialRemark_${rowNo}" placeholder="Item Remark" class="form-control m-0 boqMaterialRemark">
                                    </td>
                                    <td class="p-1 text-center">
                                        <i class="fa fa-minus bg-danger rounded p-2 removeBoqMaterialsDivItemBtn" style="cursor: pointer;"></i >
                                    </td>
                                </tr>`);

            $(`#boqMaterialId_${rowNo}`).select2();
        }

        $(document).on("change", ".rmSfgItemsDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            // console.log(selectVal);
            // console.log(rowData);
            console.log(rowDataObj);
            $(`#boqMaterialGl_${rowNo}`).val(rowDataObj["parentGlId"]);
            $(`#boqMaterialCode_${rowNo}`).val(rowDataObj["itemCode"]);
            $(`#boqMaterialType_${rowNo}`).val(rowDataObj["type"]);
            $(`#boqMaterialConsumption_${rowNo}`).val(1);
            $(`#boqMaterialExtraPurchage_${rowNo}`).val(0);
            $(`#boqMaterialUom_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#boqMaterialRate_${rowNo}`).val(rowDataObj["itemboqPrice"]);
                $(`#boqMaterialAmount_${rowNo}`).val(rowDataObj["itemboqPrice"]);
            } else {
                $(`#boqMaterialRate_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
                $(`#boqMaterialAmount_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
            }

            updateGrandTotalCost();
        });

        function calculateBoqMaterialOneRowCost(rowNo = null) {
            let rate = $(`#boqMaterialRate_${rowNo}`).val();
            let qty = $(`#boqMaterialConsumption_${rowNo}`).val();
            let extra = $(`#boqMaterialExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#boqMaterialAmount_${rowNo}`).val(itemAmount);

            updateGrandTotalCost();
        }

        $(document).on("keyup", ".boqMaterialRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateBoqMaterialOneRowCost(rowNo);
        });

        // addBoqItemMaterialNewRow();
        var boqMaterialRowNo = 0;
        $(document).on("click", ".addBoqMaterialsDivItemBtn", function() {
            addBoqItemMaterialNewRow(boqMaterialRowNo += 1);
            updateGrandTotalCost();
        });

        $(document).on("click", ".removeBoqMaterialsDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //====================================================================== [END MATERIAL] ====================================================================


        //===================================== [START HOURLY DEPLOYEMENT] ================================================
        function addBoqItemHourlyDeploymentNewRow(rowNo = 0) {
            $("#boqHourlyDeploymentDiv").append(`
                                    <tr id="boqHdDivRow_${rowNo}">
                                        <td class="p-1">
                                            <div>
                                                <select name="boqHd[${rowNo}][CostCenterId]" id="boqHdCostCenterId_${rowNo}" class="form-control boqHdCostCenterDropDown" required>
                                                    <option value="" data-row=""> -- Select Cost Center -- </option>
                                                    <?php
                                                    foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                                        echo '<option value="' . $itemObj["CostCenter_id"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqHd[${rowNo}][CostCenterCode]" id="boqHdCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 boqHdCostCenterCode" readonly>
                                        </td>
                                        <td class="p-1">
                                            <select name="boqHd[${rowNo}][ItemHdType]" id="boqHdItemHdType_${rowNo}" class="form-control boqHdItemHdTypeDropDown" required>
                                                <option value="" data-row=""> -- Select HD Type -- </option>
                                                <option value="lhr">LHR</option>
                                                <option value="mhr">MHR</option>
                                            </select>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqHd[${rowNo}][Uom]" id="boqHdUom_${rowNo}" placeholder="Item UOM" value="" class="form-control m-0 boqHdUom" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqHd[${rowNo}][Consumption]" id="boqHdConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 boqHdConsumption boqHdRowInput text-right">
                                        </td>
                                        <td class="p-1 d-flex">
                                            <input type="number" step="0.01" name="boqHd[${rowNo}][ExtraPurchage]" id="boqHdExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 boqHdExtraPurchage boqHdRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqHd[${rowNo}][Rate]" id="boqHdRate_${rowNo}" placeholder="0.00" class="form-control m-0 boqHdRate text-right" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqHd[${rowNo}][Amount]" id="boqHdAmount_${rowNo}" placeholder="0.00" class="form-control m-0 boqHdAmount text-right" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqHd[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="p-1 text-center"><i class="fa fa-minus bg-danger rounded p-2 removeBoqHdDivItemBtn" style="cursor: pointer;"></i></td>
                                    </div>`);

            $(`#boqHdCostCenterId_${rowNo}`).select2();
        }
        $(document).on("change", ".boqHdCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            $(`#boqHdCostCenterCode_${rowNo}`).val(rowDataObj["CostCenter_code"]);
            $(`#boqHdItemHdType_${rowNo}`).val($(`#boqHdItemHdType_${rowNo} option:first`).val());
            $(`#boqHdConsumption_${rowNo}`).val("");
            $(`#boqHdExtraPurchage_${rowNo}`).val("");
            $(`#boqHdRate_${rowNo}`).val("");
            $(`#boqHdAmount_${rowNo}`).val("");

            updateGrandTotalCost();
        });

        $(document).on("change", ".boqHdItemHdTypeDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(`#boqHdCostCenterId_${rowNo}`).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            let amount = 0;
            if (selectVal == "lhr") {
                amount = parseFloat(rowDataObj["labour_hour_rate"]);
            } else if (selectVal == "mhr") {
                amount = parseFloat(rowDataObj["machine_hour_rate"]);
            }
            $(`#boqHdUom_${rowNo}`).val("hour");
            $(`#boqHdConsumption_${rowNo}`).val(1);
            $(`#boqHdExtraPurchage_${rowNo}`).val(0);
            $(`#boqHdRate_${rowNo}`).val(amount);
            $(`#boqHdAmount_${rowNo}`).val(amount);

            updateGrandTotalCost();
        });

        function calculateHourlyDeploymentCost(rowNo = null) {
            console.log("Calculating HourlyDeployment cost");
            let boqHdRate = parseFloat($(`#boqHdRate_${rowNo}`).val()) > 0 ? parseFloat($(`#boqHdRate_${rowNo}`).val()) : 0;
            let boqHdConsumption = parseFloat($(`#boqHdConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#boqHdConsumption_${rowNo}`).val()) : 0;
            let boqHdExtraPurchage = parseFloat($(`#boqHdExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#boqHdExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = boqHdConsumption + (boqHdConsumption * boqHdExtraPurchage / 100);
            let amount = boqHdRate * totalQty;
            $(`#boqHdAmount_${rowNo}`).val(amount);

            updateGrandTotalCost();
        }
        $(document).on("keyup", ".boqHdRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateHourlyDeploymentCost(rowNo);
        });

        // adding/removing good activity or cost center to boq list
        // addBoqItemHourlyDeploymentNewRow();
        var boqHdRowNo = 0;
        $(document).on("click", ".addBoqHdDivItemBtn", function() {
            addBoqItemHourlyDeploymentNewRow(boqHdRowNo += 1);

            updateGrandTotalCost();
        });
        $(document).on("click", ".removeBoqHdDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //========================================== [END HOURLY DEPLOYEMENT] =============================================


        //===================================== [START OTHER HEAD] ==========================================================
        function addBoqItemOtherHeadNewRow(rowNo = 0) {
            $("#boqOtherHeadDiv").append(`
                                    <tr id="boqOtherHeadDivRow_${rowNo}">
                                        <td class="p-1">
                                            <select name="boqOtherHead[${rowNo}][CostCenterId]" id="boqOtherHeadCostCenterId_${rowNo}" class="form-control boqOtherHeadCostCenterDropDown" required>
                                                <option value="" data-row=""> -- Select Cost Center -- </option>
                                                <?php
                                                foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                                    echo '<option value="' . $itemObj["CostCenter_id"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqOtherHead[${rowNo}][CostCenterCode]" id="boqOtherHeadCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 boqOtherHeadCostCenterCode" readonly>
                                        </td>
                                        <td class="p-1">
                                            <select name="boqOtherHead[${rowNo}][Head]" id="boqOtherHead_${rowNo}" class="form-control boqOtherHeadDropDown" required>
                                                <option value="" data-row="" disabled>Other Head Loding...</option>
                                            </select>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqOtherHead[${rowNo}][Uom]" id="boqOtherHeadUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 boqOtherHeadUom" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqOtherHead[${rowNo}][Consumption]" id="boqOtherHeadConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 boqOtherHeadConsumption boqOtherHeadRowInput text-right">
                                        </td>
                                        <td class="p-1 d-flex">
                                            <input type="number" step="0.01" name="boqOtherHead[${rowNo}][ExtraPurchage]" id="boqOtherHeadExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 boqOtherHeadExtraPurchage boqOtherHeadRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqOtherHead[${rowNo}][Rate]" id="boqOtherHeadRate_${rowNo}" placeholder="0.00" class="form-control m-0 boqOtherHeadRate text-right" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="number" step="0.01" name="boqOtherHead[${rowNo}][Amount]" id="boqOtherHeadAmount_${rowNo}" placeholder="0.00" class="form-control m-0 boqOtherHeadAmount text-right" readonly>
                                        </td>
                                        <td class="p-1">
                                            <input type="text" name="boqOtherHead[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="p-1 text-center">
                                            <i class="fa fa-minus bg-danger rounded p-2 removeBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>`);
            $(`#boqOtherHeadCostCenterId_${rowNo}`).select2();
            $(`#boqOtherHead_${rowNo}`)
                .select2()
                .on('select2:open', () => {
                    $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addBomOtherHeadModal">Add New</a></div>`);
                });

            $.ajax({
                type: "GET",
                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                data: {
                    head_type: 3
                },
                success: function(response) {
                    // Handle the success response here
                    let responseData = JSON.parse(response);
                    let html = responseData.data.map((item, i) => {
                        return `<option value="${item.head_id}" data-row="${window.btoa(JSON.stringify(item))}">${item.head_name}</option>`;
                    }).join("");
                    $(`#boqOtherHead_${rowNo}`).html(`<option value="" data-row="">Select One Head...</option>${html}`);
                    // console.log(html);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error:", error);
                },
                complete: function() {}
            });
        }
        $(document).on("change", ".boqOtherHeadCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#boqOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["CostCenter_code"]);
            updateGrandTotalCost();
        });

        // $(document).on("change", ".boqOtherHeadDropDown", function() {
        //     let rowNo = ($(this).attr("id")).split("_")[1];
        //     let selectVal = $(this).val();
        //     let amount = 0;
        //     try {
        //         let rowData = $(this).find(':selected').data('row');
        //         let rowDataObj = JSON.parse(atob(rowData));
        //         let boqOtherRate = parseFloat($(`#boqOtherHeadRate_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadRate_${rowNo}`).val()) : 0;
        //         let boqOtherConsumption = parseFloat($(`#boqOtherHeadConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadConsumption_${rowNo}`).val()) : 0;
        //         let boqOtherExtraPurchage = parseFloat($(`#boqOtherHeadExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadExtraPurchage_${rowNo}`).val()) : 0;
        //         let totalQty = boqOtherConsumption + (boqOtherConsumption * boqOtherExtraPurchage / 100);
        //         amount = boqOtherRate * totalQty;
        //     } catch (e) {

        //     }
        //     $(`#boqOtherHeadConsumption_${rowNo}`).val(1);
        //     $(`#boqOtherHeadExtraPurchage_${rowNo}`).val(0);
        //     $(`#boqOtherHeadAmount_${rowNo}`).val(amount);

        //     updateGrandTotalCost();
        // });

        $(document).on("change", ".boqOtherHeadDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#boqOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["head_code"]);
            $(`#boqOtherHeadUom_${rowNo}`).val(rowDataObj["head_uom"]);
            $(`#boqOtherHeadRate_${rowNo}`).val(parseFloat(rowDataObj["head_rate"]).toFixed(2))
            $(`#boqOtherHeadConsumption_${rowNo}`).val(1);
            $(`#boqOtherHeadExtraPurchage_${rowNo}`).val(0);
            $(`#boqOtherHeadAmount_${rowNo}`).val(parseFloat(rowDataObj["head_rate"]).toFixed(2));
            updateGrandTotalCost();
        });

        function calculateOtherHeadCost(rowNo = null) {
            console.log("Calculating Other head cost");
            let boqOtherHeadRate = parseFloat($(`#boqOtherHeadRate_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadRate_${rowNo}`).val()) : 0;
            let boqOtherHeadConsumption = parseFloat($(`#boqOtherHeadConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadConsumption_${rowNo}`).val()) : 0;
            let boqOtherHeadExtraPurchage = parseFloat($(`#boqOtherHeadExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#boqOtherHeadExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = boqOtherHeadConsumption + (boqOtherHeadConsumption * boqOtherHeadExtraPurchage / 100);
            let amount = boqOtherHeadRate * totalQty;
            $(`#boqOtherHeadAmount_${rowNo}`).val(amount.toFixed(2));
        }

        $(document).on("keyup", ".boqOtherHeadRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOtherHeadCost(rowNo);
            updateGrandTotalCost();
        });


        // adding/removing other good items to boq list
        // addBoqItemOtherHeadNewRow();
        var boqOtherHeadRowNo = 0;
        $(document).on("click", ".addBoqOtherHeadDivItemBtn", function() {
            addBoqItemOtherHeadNewRow(boqOtherHeadRowNo += 1);
            updateGrandTotalCost();
        });
        $(document).on("click", ".removeBoqOtherHeadDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //============================================ [END OTHER HEAD] =============================================


        //============================================ [START ADD OTHER HEAD] =======================================

        $(document).on("submit", "#frmAddOtherExpenseHeadForm", function(event) {
            // Prevent the default form submission
            event.preventDefault();
            // Disable the submit button to prevent multiple submissions
            $("button[type=submit]").prop("disabled", true);
            // Serialize the form data
            var formData = $(this).serialize();
            // Perform an AJAX POST request to submit the form
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                data: formData,
                success: function(response) {
                    // Handle the success response here
                    let responseData = JSON.parse(response);
                    Swal.fire({
                        icon: responseData.status,
                        title: responseData.status.toUpperCase(),
                        text: responseData.message,
                    });

                    console.log(responseData);
                    // Reset the form
                    $("#frmAddOtherExpenseHeadForm")[0].reset();
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error:", error);
                },
                complete: function() {
                    // Re-enable the submit button
                    $("button[type=submit]").prop("disabled", false);
                }
            });
        });

        //============================================= [END ADD OTHER HEAD] ========================================

    });
</script>