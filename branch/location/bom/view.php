<?php
$itemId = base64_decode($_GET["view"]);
include_once("controller/bom.controller.php");
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name=$companyCurrencyData['currency_name'];
$bomControllerObj = new BomController();
$bomDetailObj = $bomControllerObj->getBomDetails($itemId);
if ($bomDetailObj["status"] != "success") {
    // console($bomDetailObj);
    swalAlert($bomDetailObj["status"], ucfirst($bomDetailObj["status"]), $bomDetailObj["message"], LOCATION_URL . "bom/bom.php?create=" . base64_encode($itemId));
} else {
?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/bom/bom.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOM</a></li>
        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-eye po-list-icon"></i> View</a></li>
        <li class="back-button">
            <a href="<?= BASE_URL ?>branch/location/bom/bom.php">
                <i class="fa fa-reply po-list-icon"></i>
            </a>
        </li>
    </ol>

    <div class="row my-4">
        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="bill-qty-section">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <h5 class="title d-flex"><?= $bomDetailObj["data"]["bom_data"]["itemCode"] ?? "" ?> - <?= $bomDetailObj["data"]["bom_data"]["itemName"] ?? "" ?></h5>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="display-flex-space-between text-nowrap gap-2">
                            <p class="text-xs">Prepared By <b><?= ucfirst($bomDetailObj["data"]["bom_data"]["preparedBy"]) ?></b></p>
                            <p class="text-xs">At  <b><?= formatDateORDateTime($bomDetailObj["data"]["bom_data"]["preparedDate"]) ?></b></p>
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
                            <p class="text-xs">Total Material Cost</p>
                            <p class="text-xs" id="grandMaterialCost"><?=$currency_name?> <?= decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm_m"]) ?></p>
                        </div>
                        <div class="display-flex-space-between">
                            <p class="text-xs">Total Activivty Cost</p>
                            <p class="text-xs" id="grandActivityCost"><?=$currency_name?> <?= decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm_a"]) ?></p>
                        </div>
                        <hr class="mt-2 mb-2">
                        <div class="display-flex-space-between">
                            <p class="text-xs font-bold">Total Cost</p>
                            <p class="text-xs font-bold" id="grandTotalCost"><?=$currency_name?> <?= decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm"]) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12 mt-2 p-0">
        <div class="">
            <div class="card">
                <div class="card-body" style="overflow: auto;">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Items</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Title</th>
                                <th>Consumption</th>
                                <th>Extra(%)</th>
                                <th>UOM</th>
                                <th>Item Rate</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($bomDetailObj["data"]["bom_material_data"] ?? [] as $bomOneItem) {
                            ?>
                                <tr>
                                    <td><?= $bomOneItem["itemCode"] ?? "" ?></td>
                                    <td><p class="pre-normal"><?= $bomOneItem["itemName"] ?? "" ?></p></td>
                                    <td><?= decimalValuePreview($bomOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["extra"]) ?></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                    <td><p class="text-right"><?= decimalValuePreview($bomOneItem["rate"]) ?></p></td>
                                    <td><p class="text-right"><?= decimalValuePreview($bomOneItem["amount"]) ?></p></td>
                                    <td><?= $bomOneItem["remarks"] ?></td>
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
                                <th>#</th>
                                <th>Work center</th>
                                <th>Code</th>
                                <th>Head Name</th>
                                <th>Consumption</th>
                                <th>Extra(%)</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($bomDetailObj["data"]["bom_hd_data"] ?? [] as $bomOneItem) {
                              //  console($bomOneItem);
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td><p class="pre-normal"><?= $bomOneItem["work_center_description"] ?>/</p></td>
                                    <td><?= $bomOneItem["work_center_code"] ?></td>
                                    <td><?= strtoupper($bomOneItem["head_type"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["extra"]) ?></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["rate"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["amount"]) ?></td>
                                    <td><?= $bomOneItem["remarks"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <p class="text-left m-0 pl-3 pb-2 font-bold">Over Heads</p>
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Work center</th>
                                <th>Code</th>
                                <th>Over Head</th>
                                <th>Consumption</th>
                                <th>Extra(%)</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($bomDetailObj["data"]["bom_other_head_data"] ?? [] as $bomOneItem) {
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td><p class="pre-normal"><?= $bomOneItem["work_center_description"] ?></p></td>
                                    <td><?= $bomOneItem["work_center_code"] ?></td>
                                    <td><?= ucfirst($bomOneItem["head_name"] ?? "") ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["consumption"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["extra"]) ?></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["rate"]) ?></td>
                                    <td><?= decimalValuePreview($bomOneItem["amount"]) ?></td>
                                    <td><?= $bomOneItem["remarks"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


            </div>

            <?php
            if ($bomDetails["bomProgressStatus"] == "COGM") {
            ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">Other Addons</p>
                        <form action="" method="post">
                            <input type="hidden" name="bomId" value="<?= $bomDetails["bomId"] ?>">
                            <table class="table mb-3">
                                <thead>
                                    <tr>
                                        <th>Others</th>
                                        <th>Select Gl</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
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
                                                    <input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemName[]" id="bomOtherAddonItemName_${rowNo}" placeholder="Item Name" required />
                                                </td>
                                                <td>
                                                    <select name="bomOtherAddonItemGl[]" id="bomOtherAddonItemGl_${rowNo}" class="form-control bomOtherAddonItemGlDropDown" required>
                                                        <option value="" data-row=""> -- Select Gl Code -- </option>
                                                        <?php
                                                        foreach ($coaObj["data"] as $itemObj) {
                                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input step="0.01" class="form-control mt-2 mb-2" type="number" name="bomOtherAddonItemPrice[]" id="bomOtherAddonItemPrice_${rowNo}" placeholder="Item Price" required />
                                                </td>
                                                <td>
                                                    <input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemRemarks[]" id="bomOtherAddonItemRemarks_${rowNo}" placeholder="Item remarks" />
                                                </td>
                                                <td>
                                                    ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addOtherAddonItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeOtherAddonItemBtn" style="cursor: pointer;"></i>`}
                                                </td>
                                            </tr>`);

                            $(`#bomOtherAddonItemGl_${rowNo}`).select2();
                        }

                        addOtherAddonsFormItem(rowNo = 0);
                        // adding other addon items to bom list
                        var otherAddonItemsRowNo = 0;
                        $(document).on("click", ".addOtherAddonItemBtn", function() {
                            addOtherAddonsFormItem(otherAddonItemsRowNo += 1);
                        });

                        // removing bom good items, activity and others from bom list
                        $(document).on("click", ".removeOtherAddonItemBtn", function() {
                            let elm = $(this).parent().parent().remove();
                        });
                    });
                </script>
            <?php
            } elseif ($bomDetails["bomProgressStatus"] == "COGS") {
            ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">COGS Items </p>
                        <table class="table mb-3">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Gl Code</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($bomItemsList as $bomOneItem) {
                                    if ($bomOneItem["bomItemType"] == "othersCogs") {
                                        // goods other item list
                                ?>
                                        <tr>
                                            <td><p class="pre-normal"><?= $bomOneItem["othersItem"] ?></p></td>
                                            <td><?= $bomOneItem["itemGl"] ?></td>
                                            <td><?= $bomOneItem["amount"] ?></td>
                                            <td><?= $bomOneItem["remarks"] ?></td>
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
                    <div class="card-body">
                        <p class="text-left pl-3 pb-2 font-bold">Discount & Margins</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="borderNone">Discount</th>
                                    <th class="bomMargin">Margin Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control mt-2 mb-2" name="bomDiscount" placeholder="Bom Discount" /></td>
                                    <td><input step="0.01" type="number" class="form-control mt-2 mb-2" name="bomMargin" placeholder="Bom margings" /></td>
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