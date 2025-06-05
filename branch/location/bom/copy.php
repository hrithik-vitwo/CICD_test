<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/bom.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOM</a></li>
    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
    <li class="back-button">
        <a href="<?= $_SERVER["PHP_SELF"] ?>">
            <i class="fa fa-reply po-list-icon"></i>
        </a>
    </li>
</ol>

<!-- All massages, logics, consoles  -->
<div class="row m-0 p-0 messages justify-content-end">
    <?php
 $getWc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id",true);
    $queryStringOfCopy = base64_decode($_GET["copy"]);
    $copyFromItemId = explode(",",$queryStringOfCopy)[0];
    $copyToItemId = explode(",",$queryStringOfCopy)[1];
    $select_bom = queryGet("SELECT * FROM `erp_bom` WHERE `itemId` = $copyFromItemId AND `locationId` = $location_id AND `bomStatus` = 'active'");
   // console($select_bom);
    $bomId = $select_bom['data']['bomId'];

    include_once("controller/bom.controller.php");
    $bomControllerObj = new BomController();
    $bomDetailObj = $bomControllerObj->getBomDetails($bomId);
    // console($bomDetailObj);
    $goodsDetailsObj = $goodsController->getGoodsDeatils($copyToItemId);
    $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
    $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? ""; 

    // $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
    // $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
    $expenseGlListObj = getAllChartOfAccounts_list_by_p($company_id, 4);

    // $getUomDetail= getUomDetail($uomId);
    $getUomListObj = getUomList('material');
    $getWcListObj = getWcList();
    //console($getWcListObj);


    include_once("controller/bom.controller.php");
    $bomControllerObj = new BomController();
    $goodMasterList = $bomControllerObj->getGoodMasterList();
    if (isset($_POST["createBomSubmitBtn"])) {
        // console($_POST);
        $createObj = $bomControllerObj->createBom($_POST);
        // console($createObj);
        if ($createObj["status"] == "success") {
            swalAlert($createObj["status"], ucfirst($createObj["status"]), $createObj["message"], LOCATION_URL . "bom.php");
        } else {
            swalAlert($createObj["status"], ucfirst($createObj["status"]), $createObj["message"]);
        }
    }
    ?>
</div>
<!-- /.All massages, logics, consoles -->

<div class="container-fluid">
    <div class="card p-0 boq-form-card bg-transparent boq-section">

        <h5 class="card-header p-2 text-sm text-light">Create Bill of Material</h5>
        <div class="card-body p-2">
            <form action="" method="post">
                <div class="row m-0 p-0">
                   
                <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Prepared By</label>
                                <input type="hidden" name="itemId" value="<?= $copyToItemId ?>">
                                <input type="text" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Prepared Date</label>
                                <input type="date"  value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Product Title</label>
                                <input type="text" name="itemTitle" value="<?= $goodTitle ?>" placeholder="Product title" class="form-control" readonly>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Product Code</label>
                                <input type="text" name="itemCode" value="<?= $goodCode ?>" placeholder="Product Code" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Work Center</label>
                                <select name="workCenter" id="workCenterDropDown" class="form-control" required>
                                    <option value="">Select Work Center</option>
                                    <?php
                                    foreach ($getWcListObj['data'] as $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="left-divider" style="border-left: 1px solid #00000033; padding-left: 1.2em;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-muted">Total Material Cost</label>
                                    <p class="h4 font-weight-bold" id="grandMaterialCost">0.00</p>
                                    <input type="hidden" name="grandMaterialCost" value="0.00" id="grandMaterialCostInput">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted">Total Hourly Deployment Cost</label>
                                    <p class="h4 font-weight-bold" id="grandHourlyDeploymentCost">0.00</p>
                                    <input type="hidden" name="grandHourlyDeploymentCost" value="0.00" id="grandHourlyDeploymentCostInput">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="text-muted">Total Over Head Cost</label>
                                    <p class="h4 font-weight-bold" id="grandOtherHeadCost">0.00</p>
                                    <input type="hidden" name="grandOtherHeadCost" value="0.00" id="grandOtherHeadCostInput">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted">Total Cost</label>
                                    <p class="h4 font-weight-bold" id="grandTotalBomCost">0.00</p>
                                    <input type="hidden" name="grandTotalBomCost" value="0.00" id="grandTotalBomCostInput">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <h5 class="row text-sm my-2 m-0 p-0 mt-2">Materials</h5>
                        <table>
                            <tbody>
                                <th>Item Title <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>Item Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>Type <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small></th>
                                <th>Consumption <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of Items"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One Item rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqMaterialsDivItemBtn" style="cursor: pointer;"></i></th>
                            </tbody>
                            <tbody id="bomMaterialDiv">
                                <?php
                                foreach ($bomDetailObj["data"]["bom_material_data"] as $rowNo => $rowData) {
                                    $rowNo += $rowNo;
                                    $itemId = $rowData["item_id"];
                                     //console($rowData);
                                ?>
                                    <tr id="bomMaterialsDivRow_<?= $rowNo ?>">
                                        <td>
                                            <input type="hidden" name="bomMaterial[<?= $rowNo ?>][ItemGl]" id="bomMaterialGl_<?= $rowNo ?>" value="<?= $rowData['parentGlId'] ?>" class="form-control">
                                            <select name="bomMaterial[<?= $rowNo ?>][ItemId]" id="bomMaterialId_<?= $rowNo ?>" class="form-control rmSfgItemsDropDown" required>
                                                <option value="" data-row=""> --Select Item-- </option>
                                                <?php
                                                foreach ($goodMasterList["data"] as $key => $itemObj) {
                                                    $isSelectedTxt = $rowData["item_id"] == $itemObj["itemId"] ? "selected" : "";
                                                    if ($itemObj["bomStatus"] == 1) {
                                                        echo '<option value="' . $itemObj["itemId"] . '" ' . $isSelectedTxt . ' data-row="' . base64_encode(json_encode($itemObj, true)) . '"  disabled>' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . '] (BOM not created)</option>';
                                                    } else {
                                                        echo '<option value="' . $itemObj["itemId"] . '" ' . $isSelectedTxt . ' data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" value="<?= $rowData["itemCode"] ?>" name="bomMaterial[<?= $rowNo ?>][Code]" id="bomMaterialCode_<?= $rowNo ?>" class="form-control m-0 bomMaterialCode" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="<?= $rowData["type"] ?>" name="bomMaterial[<?= $rowNo ?>][Type]" id="bomMaterialType_<?= $rowNo ?>" class="form-control m-0 bomMaterialType" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="<?= $rowData["uom"] ?>" name="bomMaterial[<?= $rowNo ?>][Uom]" id="bomMaterialUom_<?= $rowNo ?>" class="form-control m-0 bomMaterialUom" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomMaterial[<?= $rowNo ?>][Consumption]" id="bomMaterialConsumption_<?= $rowNo ?>" class="form-control m-0 bomMaterialConsumtion bomMaterialRowInput text-right" value="<?= $rowData["consumption"] ?>">
                                        </td>
                                        <td class="d-flex">
                                            <input type="number" step="0.01" name="bomMaterial[<?= $rowNo ?>][ExtraPurchage]" id="bomMaterialExtraPurchage_<?= $rowNo ?>" class="form-control m-0 bomMaterialExtraPurchage bomMaterialRowInput text-right" value="<?= $rowData["extra"] ?>"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomMaterial[<?= $rowNo ?>][Rate]" id="bomMaterialRate_<?= $rowNo ?>" class="form-control m-0 bomMaterialRate text-right" value="<?= $rowData["rate"] ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomMaterial[<?= $rowNo ?>][Amount]" id="bomMaterialAmount_<?= $rowNo ?>" class="form-control m-0 bomMaterialAmount text-right" value="<?= $rowData["amount"] ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bomMaterial[<?= $rowNo ?>][Remark]" id="bomMaterialRemark_<?= $rowNo ?>" placeholder="Item Remark" class="form-control m-0 bomMaterialRemark" value="<?= $rowData["remarks"] ?>">
                                        </td>
                                        <td class="text-center">
                                            <i class="fa fa-minus bg-danger rounded p-1 removeBoqMaterialsDivItemBtn" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>
                                    <script>
                                        $(document).ready(function() {
                                            $(`#bomMaterialId_<?= $rowNo ?>`).select2();
                                        });
                                    </script>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="row m-0 p-0 mt-2">Activity</div>
                        <h5 class="row m-0 ml-2 my-2 text-sm p-0">Hourly Deployment</h5>
                        <table>
                            <tbody>
                                <th>Work Center<small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work Center Name"></i></small></th>
                                <th>Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work center code"></i></small></th>
                                <th>Hourly Deployment Type <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Hourly deployment type(LHR or MHR)"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                <th>Consumption <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqHdDivItemBtn" style="cursor: pointer;"></i></th>
                            </tbody>
                            <tbody id="bomHourlyDeploymentDiv">
                                <?php
                                foreach ($bomDetailObj["data"]["bom_hd_data"] as $rowNo => $hdrowData) {
                                    $rowNo += $rowNo;
                                     //console($hdrowData);
                                    // $itemId = $rowData["item_id"];
                                ?>
                                    <tr id="bomHdDivRow_<?= $rowNo ?>">
                                        <td>
                                            <div>
                                                <select name="bomHd[<?= $rowNo ?>][CostCenterId]" id="bomHdCostCenterId_<?= $rowNo ?>" class="form-control bomHdCostCenterDropDown" required>
                                                    <option value="" data-row=""> -- Select Work Center -- </option>
                                                    <?php
                                                    foreach ($getWc["data"] as $key => $itemObj) {
                                                        $isSelectedTxt = $hdrowData["cost_center_id"] == $itemObj["work_center_id"] ? "selected" : "";

                                                        echo '<option value="' . $itemObj["work_center_id"] . '" ' . $isSelectedTxt . ' data-row="' . base64_encode(json_encode($itemObj, true)) . '"   >' . $itemObj["work_center_code"] . ' - ' . $itemObj["work_center_name"] . '</option>';

                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[<?= $rowNo ?>][CostCenterCode]" id="bomHdCostCenterCode_<?= $rowNo ?>" placeholder="Item Code" class="form-control m-0 bomHdCostCenterCode" value="<?= $hdrowData['work_center_code'] ?>" readonly>
                                        </td>
                                        <td>
                                            <select name="bomHd[<?= $rowNo ?>][ItemHdType]" id="bomHdItemHdType_<?= $rowNo ?>" class="form-control bomHdItemHdTypeDropDown" required>
                                                <option value="" data-row=""> -- Select HD Type -- </option>
                                                <option value="lhr" <?php if ($hdrowData['head_type'] == 'lhr') {
                                                                        echo 'selected';
                                                                    } ?>>LHR</option>
                                                <option value="mhr" <?php if ($hdrowData['head_type'] == 'mhr') {
                                                                        echo 'selected';
                                                                    } ?>>MHR</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[<?= $rowNo ?>][Uom]" id="bomHdUom_<?= $rowNo ?>" class="form-control m-0 bomHdUom" value="<?= $hdrowData['uom'] ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[<?= $rowNo ?>][Consumption]" id="bomHdConsumption_<?= $rowNo ?>" class="form-control m-0 bomHdConsumption bomHdRowInput text-right" value="<?= $hdrowData['consumption'] ?>">
                                        </td>
                                        <td class="d-flex">
                                            <input type="number" step="0.01" name="bomHd[<?= $rowNo ?>][ExtraPurchage]" id="bomHdExtraPurchage_<?= $rowNo ?>" class="form-control m-0 bomHdExtraPurchage bomHdRowInput text-right" value="<?= $hdrowData['extra'] ?>"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[<?= $rowNo ?>][Rate]" id="bomHdRate_<?= $rowNo ?>" class="form-control m-0 bomHdRate text-right" value="<?= $hdrowData['rate'] ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[<?= $rowNo ?>][Amount]" id="bomHdAmount_<?= $rowNo ?>" placeholder="0.00" class="form-control m-0 bomHdAmount text-right" value="<?= $hdrowData['amount'] ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[<?= $rowNo ?>][Remark]" placeholder="Activity Remark" class="form-control m-0" value="<?= $hdrowData['remarks'] ?>">
                                        </td>
                                        <td class="text-center" style="width:4%;"><i class="fa fa-minus bg-danger rounded p-1 removeBoqHdDivItemBtn" style="cursor: pointer;"></i></td>
                                    </tr>
                                    <script>
                                        $(document).ready(function() {
                                            $(`#bomHdCostCenterId_<?= $rowNo ?>`).select2();
                                        });
                                    </script>
                                <?php
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <h5 class="row m-0 ml-2 my-2 text-sm p-0">Over Head</h5>
                        <table>
                            <tbody>
                                <th>Work Center<small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work Center Name"></i></small></th>
                                <th>Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work center code"></i></small></th>
                                <th>Over Head <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Over Head deatils"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                <th>Consumption <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i></th>
                            </tbody>
                            <tbody id="bomOtherHeadDiv">
                                <?php
                                foreach ($bomDetailObj["data"]["bom_other_head_data"] as $rowNo => $hdOtherRowData) {
                                    $rowNo += $rowNo;
                                   // console($hdOtherRowData);
                                    // $itemId = $rowData["item_id"];
                                ?>
                                    <tr id="bomOtherHeadDivRow_<?= $rowNo ?>">
                                        <td>
                                            <select name="bomOtherHead[<?= $rowNo ?>][CostCenterId]" id="bomOtherHeadCostCenterId_<?= $rowNo ?>" class="form-control bomOtherHeadCostCenterDropDown" required>
                                            <option value="" data-row=""> -- Select Work Center -- </option>
                                                    <?php
                                                    foreach ($getWc["data"] as $key => $itemObj) {
                                                        $isSelectedTxt = $hdrowData["cost_center_id"] == $itemObj["work_center_id"] ? "selected" : "";
                                                    echo '<option value="' . $itemObj["work_center_id"] . '" ' . $isSelectedTxt . ' data-row="' . base64_encode(json_encode($itemObj, true)) . '"   >' . $itemObj["work_center_code"] . ' - ' . $itemObj["work_center_name"] . '</option>';

                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[<?= $rowNo ?>][CostCenterCode]" id="bomOtherHeadCostCenterCode_<?= $rowNo ?>" placeholder="Item Code" class="form-control m-0 bomOtherHeadCostCenterCode" value="<?= $hdOtherRowData['work_center_code'] ?>" readonly>
                                        </td>
                                        <td class="bomOtherHeadDropDownTr" id="bomOtherHeadDropDownTr_<?= $rowNo ?>" style="width: 10%;">
                                            <select name="bomOtherHead[<?= $rowNo ?>][Head]" id="bomOtherHead_<?= $rowNo ?>" class="form-control bomOtherHeadDropDown" required>
                                                <option value="" data-row="" disabled>Loding Head...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[<?= $rowNo ?>][Uom]" id="bomOtherHeadUom_<?= $rowNo ?>" value="<?= $hdOtherRowData['uom'] ?>" class="form-control m-0 bomOtherHeadUom" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[<?= $rowNo ?>][Consumption]" id="bomOtherHeadConsumption_<?= $rowNo ?>" value="<?= $hdOtherRowData['consumption'] ?>" class="form-control m-0 bomOtherHeadConsumption bomOtherHeadRowInput text-right">
                                        </td>
                                        <td class="d-flex">
                                            <input type="number" step="0.01" name="bomOtherHead[<?= $rowNo ?>][ExtraPurchage]" id="bomOtherHeadExtraPurchage_<?= $rowNo ?>" value="<?= $hdOtherRowData['extra'] ?>" class="form-control m-0 bomOtherHeadExtraPurchage bomOtherHeadRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[<?= $rowNo ?>][Rate]" value="<?= $hdOtherRowData['rate'] ?>" id="bomOtherHeadRate_<?= $rowNo ?>" placeholder="0.00" class="form-control m-0 bomOtherHeadRate text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[<?= $rowNo ?>][Amount]" value="<?= $hdOtherRowData['amount'] ?>" id="bomOtherHeadAmount_<?= $rowNo ?>" placeholder="0.00" class="form-control m-0 bomOtherHeadAmount text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[<?= $rowNo ?>][Remark]" value="<?= $hdOtherRowData['remarks'] ?>" class="form-control m-0">
                                        </td>
                                        <td class="text-center" style="width: 4%;">
                                            <i class="fa fa-minus bg-danger rounded p-1 removeBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>
                                    <script>
                                        $(document).ready(function() {
                                            let tempHeadId = `<?= $hdOtherRowData["head_id"] ?>`;
                                            $(`#bomOtherHeadCostCenterId_<?= $rowNo ?>`).select2();
                                            $(`#bomOtherHead_<?= $rowNo ?>`)
                                                .select2()
                                                .on('select2:open', () => {
                                                    $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addBomOtherHeadModal">Add New</a></div>`);
                                                });

                                            $.ajax({
                                                type: "GET",
                                                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                                                data: {
                                                    head_type: 1
                                                },
                                                success: function(response) {
                                                    // Handle the success response here
                                                    let responseData = JSON.parse(response);
                                                    let html = responseData.data.map((item, i) => {
                                                        let isSelectedTxt = (tempHeadId == item.head_id) ? "selected" : "";
                                                        return `<option value="${item.head_id}" ${isSelectedTxt} data-row="${window.btoa(JSON.stringify(item))}">${item.head_name}</option>`;
                                                    }).join("");
                                                    $(`#bomOtherHead_<?= $rowNo ?>`).html(`<option value="" data-row="">Select One Head...</option>${html}`);
                                                    // console.log(html);
                                                },
                                                error: function(xhr, status, error) {
                                                    // Handle errors here
                                                    console.error("Error:", error);
                                                },
                                                complete: function() {}
                                            });
                                        });
                                    </script>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer m-0 p-0 text-right">
                    <button type="submit" value="Save" name="createBomSubmitBtn" class="btn btn-primary text-light my-3">Save BOM</button>
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-danger">Back</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Add New Over Head Modal -->
    <div class="modal fade" id="addBomOtherHeadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Activity Over Head</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="p-0 m-0" id="frmAddOtherExpenseHeadForm">
                    <div class="modal-body">
                        <input type="hidden" name="frmOtherHeadType" value="1">
                        <div class="form-input">
                            <label for="">Head Code</label>
                            <input type="text" name="frmOtherHeadCode" placeholder="Code" class="form-control" required>
                        </div>

                        <div class="form-input">
                            <label for="">Name</label>
                            <input type="text" name="frmOtherHeadName" placeholder="Name" class="form-control" required>
                        </div>

                        <div class="form-input">
                            <label for="">Connect Gl <small>(Optional, it will use for analytics)</small></label>
                            <select name="frmOtherHeadGl" class="form-control" id="frmOtherHeadGlDropDown">
                                <option value="">Select Gl</option>
                                <?php
                                foreach ($expenseGlListObj["data"] as $row) {
                                ?>
                                    <option value="<?= $row["id"] ?>"><?= $row["gl_label"] ?> - <?= $row["gl_code"] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

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
    <!-- END New Over Head Modal -->
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
            $(".bomMaterialAmount").each(function() {
                grandMaterialCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            let grandHourlyDeploymentCost = 0;
            $(".bomHdAmount").each(function() {
                grandHourlyDeploymentCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            let grandOtherHeadCost = 0;
            $(".bomOtherHeadAmount").each(function() {
                grandOtherHeadCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            $("#grandMaterialCost").html(grandMaterialCost.toFixed(2));
            $("#grandHourlyDeploymentCost").html(grandHourlyDeploymentCost.toFixed(2));
            $("#grandOtherHeadCost").html(grandOtherHeadCost.toFixed(2));

            $("#grandMaterialCostInput").val(grandMaterialCost.toFixed(2));
            $("#grandHourlyDeploymentCostInput").val(grandHourlyDeploymentCost.toFixed(2));
            $("#grandOtherHeadCostInput").val(grandOtherHeadCost.toFixed(2));

            $("#grandTotalBomCost").html((grandMaterialCost + grandHourlyDeploymentCost + grandOtherHeadCost).toFixed(2));
            $("#grandTotalBomCostInput").val((grandMaterialCost + grandHourlyDeploymentCost + grandOtherHeadCost).toFixed(2));
        }
        //============================================= [END UPDATE GRAND COST] =====================================================


        //================================================ [START MATERIAL] =========================================================
        function addBoqItemMaterialNewRow(rowNo = 0) {
            $("#bomMaterialDiv").append(`<tr id="bomMaterialsDivRow_${rowNo}">
                                    <td>
                                        <input type="hidden" name="bomMaterial[${rowNo}][ItemGl]" id="bomMaterialGl_${rowNo}" value="0" class="form-control">
                                        <select name="bomMaterial[${rowNo}][ItemId]" id="bomMaterialId_${rowNo}" class="form-control rmSfgItemsDropDown" required>
                                            <option value="" data-row=""> --Select Item-- </option>
                                            <?php
                                            foreach ($goodMasterList["data"] as $key => $itemObj) {
                                                if ($itemObj["itemId"] == $itemId) continue;
                                                if ($itemObj["bomStatus"] == 1) {
                                                    echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  disabled>' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . '] (BOM not created)</option>';
                                                } else {
                                                    echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type = "text" name = "bomMaterial[${rowNo}][Code]" id = "bomMaterialCode_${rowNo}" placeholder = "Item Code" class="form-control m-0 bomMaterialCode" readonly >
                                    </td>
                                    <td>
                                        <input type = "text" name = "bomMaterial[${rowNo}][Type]" id = "bomMaterialType_${rowNo}" placeholder = "Item Type" class="form-control m-0 bomMaterialType" readonly >
                                    </td>
                                    <td>
                                        <input type = "text" name = "bomMaterial[${rowNo}][Uom]" id = "bomMaterialUom_${rowNo}" placeholder = "Item UOM" class="form-control m-0 bomMaterialUom" readonly >
                                    </td>
                                    <td>
                                        <input type = "number" step = "0.01" name = "bomMaterial[${rowNo}][Consumption]" id = "bomMaterialConsumption_${rowNo}" placeholder = "0.00" class = "form-control m-0 bomMaterialConsumtion bomMaterialRowInput text-right" >
                                    </td>
                                    <td class="d-flex">
                                        <input type = "number" step = "0.01" name = "bomMaterial[${rowNo}][ExtraPurchage]" id = "bomMaterialExtraPurchage_${rowNo}" placeholder = "0.00" class = "form-control m-0 bomMaterialExtraPurchage bomMaterialRowInput text-right" ><span class = "text-muted mt-1 ml-1">%</span>
                                    </td>
                                    <td>
                                        <input type = "number" step = "0.01" name = "bomMaterial[${rowNo}][Rate]" value="0.00" id = "bomMaterialRate_${rowNo}" placeholder = "0.00" class = "form-control m-0 bomMaterialRate text-right" readonly >
                                    </td>
                                    <td>
                                        <input type = "number" step = "0.01" name = "bomMaterial[${rowNo}][Amount]" value="0.00" id = "bomMaterialAmount_${rowNo}" placeholder = "0.00" class = "form-control m-0 bomMaterialAmount text-right" readonly >
                                    </td>
                                    <td>
                                        <input type="text" name="bomMaterial[${rowNo}][Remark]" id="bomMaterialRemark_${rowNo}" placeholder="Item Remark" class="form-control m-0 bomMaterialRemark">
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-minus bg-danger rounded p-1 removeBoqMaterialsDivItemBtn" style="cursor: pointer;"></i >
                                    </td>
                                </tr>`);

            $(`#bomMaterialId_${rowNo}`).select2();
        }

        $(document).on("change", ".rmSfgItemsDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            // console.log(selectVal);
            // console.log(rowData);
            console.log(rowDataObj);
            $(`#bomMaterialGl_${rowNo}`).val(rowDataObj["parentGlId"]);
            $(`#bomMaterialCode_${rowNo}`).val(rowDataObj["itemCode"]);
            $(`#bomMaterialType_${rowNo}`).val(rowDataObj["type"]);
            $(`#bomMaterialConsumption_${rowNo}`).val(1);
            $(`#bomMaterialExtraPurchage_${rowNo}`).val(0);
            $(`#bomMaterialUom_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#bomMaterialRate_${rowNo}`).val(rowDataObj["itemBomPrice"]);
                $(`#bomMaterialAmount_${rowNo}`).val(rowDataObj["itemBomPrice"]);
            } else {
                $(`#bomMaterialRate_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
                $(`#bomMaterialAmount_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
            }
            updateGrandTotalCost();
        });

        function calculateBoqMaterialOneRowCost(rowNo = null) {
            let rate = $(`#bomMaterialRate_${rowNo}`).val();
            let qty = $(`#bomMaterialConsumption_${rowNo}`).val();
            let extra = $(`#bomMaterialExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#bomMaterialAmount_${rowNo}`).val(itemAmount.toFixed(2));
        }

        $(document).on("keyup", ".bomMaterialRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateBoqMaterialOneRowCost(rowNo);
            updateGrandTotalCost();
        });

        // addBoqItemMaterialNewRow();
        var bomMaterialRowNo = 0;
        $(document).on("click", ".addBoqMaterialsDivItemBtn", function() {
            addBoqItemMaterialNewRow(bomMaterialRowNo += 1);
            updateGrandTotalCost();
        });

        $(document).on("click", ".removeBoqMaterialsDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });

        updateGrandTotalCost();
        //====================================================================== [END MATERIAL] ====================================================================


        //===================================== [START HOURLY DEPLOYEMENT] ================================================
        function addBoqItemHourlyDeploymentNewRow(rowNo = 0) {
            $("#bomHourlyDeploymentDiv").append(`
                                    <tr id="bomHdDivRow_${rowNo}">
                                        <td>
                                            <div>
                                                <select name="bomHd[${rowNo}][CostCenterId]" id="bomHdCostCenterId_${rowNo}" class="form-control bomHdCostCenterDropDown" required>
                                                <option value="" data-row=""> -- Select Work Center -- </option>
                                                    <?php
                                    foreach ($getWc['data'] as $key => $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>" data-row="<?php echo base64_encode(json_encode($wc, true)); ?>" ><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[${rowNo}][CostCenterCode]" id="bomHdCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomHdCostCenterCode" readonly>
                                        </td>
                                        <td>
                                            <select name="bomHd[${rowNo}][ItemHdType]" id="bomHdItemHdType_${rowNo}" class="form-control bomHdItemHdTypeDropDown" required>
                                                <option value="" data-row=""> -- Select HD Type -- </option>
                                                <option value="lhr">LHR</option>
                                                <option value="mhr">MHR</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[${rowNo}][Uom]" id="bomHdUom_${rowNo}" placeholder="Item UOM" value="" class="form-control m-0 bomHdUom" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[${rowNo}][Consumption]" id="bomHdConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdConsumption bomHdRowInput text-right">
                                        </td>
                                        <td class="d-flex">
                                            <input type="number" step="0.01" name="bomHd[${rowNo}][ExtraPurchage]" id="bomHdExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdExtraPurchage bomHdRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[${rowNo}][Rate]" value="0.00" id="bomHdRate_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdRate text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomHd[${rowNo}][Amount]" value="0.00" id="bomHdAmount_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdAmount text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bomHd[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="text-center" style="width:4%;"><i class="fa fa-minus bg-danger rounded p-1 removeBoqHdDivItemBtn" style="cursor: pointer;"></i></td>
                                    </div>`);

            $(`#bomHdCostCenterId_${rowNo}`).select2();
        }
        $(document).on("change", ".bomHdCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            $(`#bomHdCostCenterCode_${rowNo}`).val(rowDataObj["work_center_code"]);
            $(`#bomHdItemHdType_${rowNo}`).val($(`#bomHdItemHdType_${rowNo} option:first`).val());
            $(`#bomHdConsumption_${rowNo}`).val("");
            $(`#bomHdExtraPurchage_${rowNo}`).val("");
            $(`#bomHdRate_${rowNo}`).val("");
            $(`#bomHdAmount_${rowNo}`).val("");
        });

        $(document).on("change", ".bomHdItemHdTypeDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(`#bomHdCostCenterId_${rowNo}`).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            let amount = 0;
            if (selectVal == "lhr") {
                amount = parseFloat(rowDataObj["wc_lhr"]);
            } else if (selectVal == "mhr") {
                amount = parseFloat(rowDataObj["wc_mhr"]);
            }
            $(`#bomHdUom_${rowNo}`).val("hour");
            $(`#bomHdConsumption_${rowNo}`).val(1);
            $(`#bomHdExtraPurchage_${rowNo}`).val(0);
            $(`#bomHdRate_${rowNo}`).val(amount);
            $(`#bomHdAmount_${rowNo}`).val(amount);

            updateGrandTotalCost();
        });

        function calculateHourlyDeploymentCost(rowNo = null) {
            console.log("Calculating HourlyDeployment cost");
            let bomHdRate = parseFloat($(`#bomHdRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdRate_${rowNo}`).val()) : 0;
            let bomHdConsumption = parseFloat($(`#bomHdConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdConsumption_${rowNo}`).val()) : 0;
            let bomHdExtraPurchage = parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = bomHdConsumption + (bomHdConsumption * bomHdExtraPurchage / 100);
            let amount = bomHdRate * totalQty;
            $(`#bomHdAmount_${rowNo}`).val(amount.toFixed(2));
        }
        $(document).on("keyup", ".bomHdRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateHourlyDeploymentCost(rowNo);
            updateGrandTotalCost();
        });

        // adding/removing good activity or cost center to bom list
        // addBoqItemHourlyDeploymentNewRow();
        var bomHdRowNo = 0;
        $(document).on("click", ".addBoqHdDivItemBtn", function() {
            addBoqItemHourlyDeploymentNewRow(bomHdRowNo += 1);
            updateGrandTotalCost();
        });
        $(document).on("click", ".removeBoqHdDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //========================================== [END HOURLY DEPLOYEMENT] =============================================


        //===================================== [START OTHER HEAD] ==========================================================
        function addBoqItemOtherHeadNewRow(rowNo = 0) {
            $("#bomOtherHeadDiv").append(`
                                    <tr id="bomOtherHeadDivRow_${rowNo}">
                                        <td>
                                            <select name="bomOtherHead[${rowNo}][CostCenterId]" id="bomOtherHeadCostCenterId_${rowNo}" class="form-control bomOtherHeadCostCenterDropDown" required>
                                            <option value="" data-row=""> -- Select Work Center -- </option>
                                                    <?php
                                    foreach ($getWc['data'] as $key => $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>" data-row="<?php echo base64_encode(json_encode($wc, true)); ?>" ><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[${rowNo}][CostCenterCode]" id="bomOtherHeadCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomOtherHeadCostCenterCode" readonly>
                                        </td>
                                        <td class="bomOtherHeadDropDownTr" id="bomOtherHeadDropDownTr_${rowNo}" style="width: 10%;">
                                            <select name="bomOtherHead[${rowNo}][Head]" id="bomOtherHead_${rowNo}" class="form-control bomOtherHeadDropDown" required>
                                                <option value="" data-row="" disabled>Loding Head... </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[${rowNo}][Uom]" id="bomOtherHeadUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 bomOtherHeadUom" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[${rowNo}][Consumption]" id="bomOtherHeadConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadConsumption bomOtherHeadRowInput text-right">
                                        </td>
                                        <td class="d-flex">
                                            <input type="number" step="0.01" name="bomOtherHead[${rowNo}][ExtraPurchage]" id="bomOtherHeadExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadExtraPurchage bomOtherHeadRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[${rowNo}][Rate]" value="0.00" id="bomOtherHeadRate_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadRate text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="bomOtherHead[${rowNo}][Amount]" value="0.00" id="bomOtherHeadAmount_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadAmount text-right" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="bomOtherHead[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="text-center" style="width: 4%;">
                                            <i class="fa fa-minus bg-danger rounded p-1 removeBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>`);
            $(`#bomOtherHeadCostCenterId_${rowNo}`).select2();
            $(`#bomOtherHead_${rowNo}`)
                .select2()
                .on('select2:open', () => {
                    $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addBomOtherHeadModal">Add New</a></div>`);
                });

            $.ajax({
                type: "GET",
                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                data: {
                    head_type: 1
                },
                success: function(response) {
                    // Handle the success response here
                    let responseData = JSON.parse(response);
                    let html = responseData.data.map((item, i) => {
                        return `<option value="${item.head_id}" data-row="${window.btoa(JSON.stringify(item))}">${item.head_name}</option>`;
                    }).join("");
                    $(`#bomOtherHead_${rowNo}`).html(`<option value="" data-row="">Select One Head...</option>${html}`);
                    // console.log(html);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error:", error);
                },
                complete: function() {}
            });
        }
        $(document).on("change", ".bomOtherHeadCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#bomOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["work_center_code"]);
        });

        $(document).on("change", ".bomOtherHeadDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#bomOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["head_code"]);
            $(`#bomOtherHeadUom_${rowNo}`).val(rowDataObj["head_uom"]);
            $(`#bomOtherHeadRate_${rowNo}`).val(parseFloat(rowDataObj["head_rate"]).toFixed(2))
            $(`#bomOtherHeadConsumption_${rowNo}`).val(1);
            $(`#bomOtherHeadExtraPurchage_${rowNo}`).val(0);
            $(`#bomOtherHeadAmount_${rowNo}`).val(parseFloat(rowDataObj["head_rate"]).toFixed(2));
            updateGrandTotalCost();
        });

        function calculateOtherHeadCost(rowNo = null) {
            console.log("Calculating Other head cost");
            let bomOtherHeadRate = parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) : 0;
            let bomOtherHeadConsumption = parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) : 0;
            let bomOtherHeadExtraPurchage = parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = bomOtherHeadConsumption + (bomOtherHeadConsumption * bomOtherHeadExtraPurchage / 100);
            let amount = bomOtherHeadRate * totalQty;
            $(`#bomOtherHeadAmount_${rowNo}`).val(amount.toFixed(2));
        }

        $(document).on("keyup", ".bomOtherHeadRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOtherHeadCost(rowNo);
            updateGrandTotalCost();
        });

        // adding/removing other good items to bom list
        // addBoqItemOtherHeadNewRow();
        var bomOtherHeadRowNo = 0;
        $(document).on("click", ".addBoqOtherHeadDivItemBtn", function() {
            addBoqItemOtherHeadNewRow(bomOtherHeadRowNo += 1);
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

                    // console.log(responseData);
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