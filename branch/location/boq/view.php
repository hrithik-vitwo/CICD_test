<?php
$itemId = base64_decode($_GET["view"]);
$boqDetailObj = $boqControllerObj->getBoqDetails($itemId);
// console($boqDetailObj);
if ($boqDetailObj["status"] != "success") {
    swalAlert($boqDetailObj["status"], ucfirst($boqDetailObj["status"]), $boqDetailObj["message"], LOCATION_URL . "boq/boq.php?create=" . base64_encode($itemId));
} else {
?>
    <div class="col-12 mt-2 p-0">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/boq/boq.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOQ</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-eye po-list-icon"></i> View</a></li>
            <li class="back-button">
                <a href="<?= BASE_URL ?>branch/location/boq/boq.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>

        <div class="row my-4">
            <div class="col-lg-5 col-md-5 col-sm-5">
                <div class="bill-qty-section">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <h5 class="title d-flex"><?= $boqDetailObj["data"]["boq_data"]["itemCode"] ?? "" ?> - <?= $boqDetailObj["data"]["boq_data"]["itemName"] ?? "" ?></h5>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="display-flex-space-between text-nowrap gap-2">
                                <p class="text-xs">Prepared By <b><?= ucfirst($boqDetailObj["data"]["boq_data"]["preparedBy"]) ?></b></p>
                                <p class="text-xs">At <b><?= formatDateORDateTime($boqDetailObj["data"]["boq_data"]["preparedDate"]) ?></b></p>
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

                        <?php
                        
                        // console($boqDetailObj);
                        ?>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="display-flex-space-between">
                                <p class="text-xs">Total Material Cost</p>
                                <p class="text-xs" id="grandMaterialCost">Rs <?= decimalValuePreview($boqDetailObj["data"]["boq_data"]["cosp_m"]) ?></p>
                            </div>
                            <div class="display-flex-space-between">
                                <p class="text-xs">Total Incoming Service Cost</p>
                                <p class="text-xs" id="grandMaterialCost">Rs <?= decimalValuePreview($boqDetailObj["data"]["boq_data"]["cosp_i"]) ?></p>
                            </div>
                            <div class="display-flex-space-between">
                                <p class="text-xs">Total Activivty Cost</p>
                                <p class="text-xs" id="grandActivityCost">Rs <?= decimalValuePreview($boqDetailObj["data"]["boq_data"]["cosp_a"]) ?></p>
                            </div>
                            <hr class="mt-2 mb-2">
                            <div class="display-flex-space-between">
                                <p class="text-xs font-bold">Total Cost</p>
                                <p class="text-xs font-bold" id="grandTotalCost">Rs <?= decimalValuePreview($boqDetailObj["data"]["boq_data"]["cogm"]) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="">
            <div class="card">
                <div class="card-body" style="overflow: auto;">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Service Items</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="borderNone">Item Code</th>
                                <th class="borderNone">Item Title</th>
                                <th class="borderNone">Consumption</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                                <th class="borderNone">Item Rate</th>
                                <th class="borderNone">Amount</th>
                                <th class="borderNone">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($boqDetailObj["data"]["boq_service_data"] ?? [] as $boqOneItem) {
                            ?>
                                <tr>
                                    <td><?= $boqOneItem["itemCode"] ?? "" ?></td>
                                    <td><p class="pre-normal"><?= $boqOneItem["itemName"] ?? "" ?></p></td>
                                    <td><?= decimalValuePreview($boqOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["extra"]) ?></td>
                                    <td><?= $boqOneItem["uom"] ?></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["rate"]) ?></p></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["amount"]) ?></p></td>
                                    <td><?= $boqOneItem["remarks"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="overflow: auto;">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Goods Items</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="borderNone">Item Code</th>
                                <th class="borderNone">Item Title</th>
                                <th class="borderNone">Consumption</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                                <th class="borderNone">Item Rate</th>
                                <th class="borderNone">Amount</th>
                                <th class="borderNone">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($boqDetailObj["data"]["boq_material_data"] ?? [] as $boqOneItem) {
                            ?>
                                <tr>
                                    <td><?= $boqOneItem["itemCode"] ?? "" ?></td>
                                    <td><p class="pre-normal"><?= $boqOneItem["itemName"] ?? "" ?></p></td>
                                    <td><?= decimalValuePreview($boqOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["extra"]) ?></td>
                                    <td><?= $boqOneItem["uom"] ?></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["rate"]) ?></p></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["amount"]) ?></p></td>
                                    <td><?= $boqOneItem["remarks"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="overflow: auto;">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Activities</p>
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Hourly Deployment</p>
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th class="borderNone">#</th>
                                <th class="borderNone">Cost center</th>
                                <th class="borderNone">Code</th>
                                <th class="borderNone">Head Name</th>
                                <th class="borderNone">Consumption</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                                <th class="borderNone">Rate</th>
                                <th class="borderNone">Amount</th>
                                <th class="borderNone">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($boqDetailObj["data"]["boq_hd_data"] ?? [] as $boqOneItem) {
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td><p class="pre-normal"><?= $boqOneItem["CostCenter_desc"] ?></p></td>
                                    <td><?= $boqOneItem["CostCenter_code"] ?></td>
                                    <td><?= strtoupper($boqOneItem["head_type"]) ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["extra"]) ?></td>
                                    <td><?= $boqOneItem["uom"] ?></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["rate"]) ?></p></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["amount"]) ?></p></td>
                                    <td><?= $boqOneItem["remarks"]??"-" ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <p class="text-left m-0 pl-3 pb-2 font-bold">Other Heads</p>
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th class="borderNone">#</th>
                                <th class="borderNone">Cost center</th>
                                <th class="borderNone">Code</th>
                                <th class="borderNone">Other Head</th>
                                <th class="borderNone">Consumption</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                                <th class="borderNone">Rate</th>
                                <th class="borderNone">Amount</th>
                                <th class="borderNone">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($boqDetailObj["data"]["boq_other_head_data"] ?? [] as $boqOneItem) {
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td><p class="pre-normal"><?= $boqOneItem["CostCenter_desc"] ?></p></td>
                                    <td><?= $boqOneItem["CostCenter_code"] ?></td>
                                    <td><?= ucfirst($boqOneItem["head_name"] ?? "") ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($boqOneItem["extra"]) ?></td>
                                    <td><?= $boqOneItem["uom"] ?></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["rate"]) ?></p></td>
                                    <td><p class="text-right"><?= decimalValuePreview($boqOneItem["amount"]) ?></p></td>
                                    <td><?= $boqOneItem["remarks"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


            </div>

            <?php
            if ($boqDetails["boqProgressStatus"] == "COGM") {
            ?>
                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">Other Addons</p>
                        <form action="" method="post">
                            <input type="hidden" name="boqId" value="<?= $boqDetails["boqId"] ?>">
                            <table class="table mb-3">
                                <thead>
                                    <tr>
                                        <th class="borderNone">Others</th>
                                        <th class="borderNone">Select Gl</th>
                                        <th class="borderNone">Amount</th>
                                        <th class="borderNone">Remarks</th>
                                        <th class="borderNone">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="otherAddonsForm">
                                </tbody>
                            </table>
                            <button type="submit" name="addCOGSFormSubmitBtn" class="btn btn-sm btn-primary text-xs mb-4 mr-3 float-right" value="Create COGS">Create COGS</button>
                        </form>

                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        function addOtherAddonsFormItem(rowNo = 0) {
                            $("#otherAddonsForm").append(`
                                            <tr id="otherAddonItemTr_${rowNo}">
                                                <td>
                                                    <input class="form-control mt-2 mb-2" type="text" name="boqOtherAddonItemName[]" id="boqOtherAddonItemName_${rowNo}" placeholder="Item Name" required />
                                                </td>
                                                <td>
                                                    <select name="boqOtherAddonItemGl[]" id="boqOtherAddonItemGl_${rowNo}" class="form-control boqOtherAddonItemGlDropDown" required>
                                                        <option value="" data-row=""> -- Select Gl Code -- </option>
                                                        <?php
                                                        foreach ($coaObj["data"] as $itemObj) {
                                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input step="0.01" class="form-control mt-2 mb-2" type="number" name="boqOtherAddonItemPrice[]" id="boqOtherAddonItemPrice_${rowNo}" placeholder="Item Price" required />
                                                </td>
                                                <td>
                                                    <input class="form-control mt-2 mb-2" type="text" name="boqOtherAddonItemRemarks[]" id="boqOtherAddonItemRemarks_${rowNo}" placeholder="Item remarks" />
                                                </td>
                                                <td>
                                                    ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addOtherAddonItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeOtherAddonItemBtn" style="cursor: pointer;"></i>`}
                                                </td>
                                            </tr>`);

                            $(`#boqOtherAddonItemGl_${rowNo}`).select2();
                        }

                        addOtherAddonsFormItem(rowNo = 0);
                        // adding other addon items to boq list
                        var otherAddonItemsRowNo = 0;
                        $(document).on("click", ".addOtherAddonItemBtn", function() {
                            addOtherAddonsFormItem(otherAddonItemsRowNo += 1);
                        });

                        // removing boq good items, activity and others from boq list
                        $(document).on("click", ".removeOtherAddonItemBtn", function() {
                            let elm = $(this).parent().parent().remove();
                        });
                    });
                </script>
            <?php
            } elseif ($boqDetails["boqProgressStatus"] == "COGS") {
            ?>
                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">COGS Items </p>
                        <table class="table mb-3">
                            <thead>
                                <tr>
                                    <th class="borderNone">Name</th>
                                    <th class="borderNone">Gl Code</th>
                                    <th class="borderNone">Amount</th>
                                    <th class="borderNone">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($boqItemsList as $boqOneItem) {
                                    if ($boqOneItem["boqItemType"] == "othersCogs") {
                                        // goods other item list
                                ?>
                                        <tr>
                                            <td><?= $boqOneItem["othersItem"] ?></td>
                                            <td><?= $boqOneItem["itemGl"] ?></td>
                                            <td><?= decimalValuePreview ($boqOneItem["amount"]) ?></td>
                                            <td><?= $boqOneItem["remarks"] ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="overflow: auto;">
                        <p class="text-left pl-3 pb-2 font-bold">Discount & Margins</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="borderNone">Discount</th>
                                    <th class="boqMargin">Margin Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control mt-2 mb-2" name="boqDiscount" placeholder="boq Discount" /></td>
                                    <td><input step="0.01" type="number" class="form-control mt-2 mb-2" name="boqMargin" placeholder="boq margings" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
            } ?>

        </div>
    </div>
<?php
}
?>