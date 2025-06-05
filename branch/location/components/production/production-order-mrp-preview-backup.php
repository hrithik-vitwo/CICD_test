<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/manage-production-order.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Production Order</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-eye po-list-icon"></i> MRP Preview</a></li>
                <li class="back-button">
                    <a href="<?= BASE_URL ?>branch/location/manage-production-order.php">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>

            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="card bg-transparent">
                        <div class="card-body">
                            <?php
                            if (isset($_POST["confirmAndReleaseOrderBtn"])) {
                                // console($_POST);
                                // exit();
                                try {
                                    $mrpControllerObj = new MrpController();


                                    $confirmObj = $mrpControllerObj->confirmAndReleaseMrp($_POST);

                                    swalAlert($confirmObj["status"], ucfirst($confirmObj["status"]), $confirmObj["message"], $_SERVER["PHP_SELF"]);
                                    // console($confirmObj);
                                } catch (Exception $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                            }

                            ?>
                            <form action="" method="post" id="confirmAndReleaseOrderFrm">
                                <?php
                                if (isset($_GET["run-mrp"]) && $_GET["run-mrp"] != "") {
                                    $productionOrdersIdArr = explode(",", base64_decode($_GET["run-mrp"]));
                                    //console(base64_decode($_GET["run-mrp"]));



                                    $mrpControllerObj = new MrpController();

                                    $prodDetail = $mrpControllerObj->getProductionOrder($productionOrdersIdArr);
                                    $main_fg = $prodDetail['data'][0];
                                    // console($prodDetail);

                                    $previewMrpObj = $mrpControllerObj->previewMrp($productionOrdersIdArr);
                                    // console($previewMrpObj);
                                    echo 'Item - ' . $main_fg['itemName'];
                                    echo '<br> Qty - ' . $main_fg['qty'];



                                    if ($previewMrpObj["status"] == "success") { ?>
                                        <div class="row p-0 m-1">
                                            <div class="col-6 pl-0">RM List</div>
                                            <div class="col-6 pr-0 text-right d-flex justify-content-end"><span class="mr-2 text-xs">All RM required on</span><input id="rmRequiredDate" type="date" value="<?= date("Y-m-d") ?>" name="rmRequiredDate" class="form-control col-md-3"></div>
                                        </div>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th class="borderNone">Sl</th>
                                                    <th class="borderNone">Item Code</th>
                                                    <th class="borderNone">Item Title</th>
                                                    <th class="borderNone">Total Consumption</th>
                                                    <th class="borderNone">Extra Required</th>
                                                    <th class="borderNone">UOM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($previewMrpObj["bomItems"] as $listRow) {
                                                    if ($listRow["goodsType"] != 1) {
                                                        continue;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $sl += 1 ?></td>
                                                        <td><?= $listRow["itemCode"] ?></td>
                                                        <td><?= $listRow["itemName"] ?></td>
                                                        <td><?= number_format($listRow["totalConsumption"], 2) ?></td>
                                                        <td><?= number_format($listRow["extraRequired"] ?? 0, 2) ?></td>
                                                        <td><?= ucfirst($listRow["uom"]) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div class="row p-0 m-1 mt-3">
                                            <div class="col-6 pl-0">SFG List</div>
                                            <div class="col-6 pr-0 text-right d-flex justify-content-end"><span class="mr-2 text-xs">All SFG required on</span><input id="sfgRequiredDate" type="date" value="<?= date("Y-m-d") ?>" name="sfgRequiredDate" class="form-control col-md-3"></div>
                                        </div>

                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th class="borderNone">Sl</th>
                                                    <th class="borderNone">Item Code</th>
                                                    <th class="borderNone">Item Title</th>
                                                    <th class="borderNone">Total Consumption</th>
                                                    <th class="borderNone">Extra Required</th>
                                                    <th class="borderNone">UOM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($previewMrpObj["bomItems"] as $listRow) {
                                                    if ($listRow["goodsType"] != 2) {
                                                        continue;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $sl += 1 ?></td>
                                                        <td><?= $listRow["itemCode"] ?></td>
                                                        <td><?= $listRow["itemName"] ?></td>
                                                        <td><?= number_format($listRow["totalConsumption"], 2) ?></td>
                                                        <td><?= number_format($listRow["extraRequired"] ?? 0, 2) ?></td>
                                                        <td><?= ucfirst($listRow["uom"]) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div class="row p-0 m-1 mt-3">
                                            <div class="col-6 pl-0">FG List</div>
                                            <div class="col-6 pr-0 text-right d-flex justify-content-end"><span class="mr-2 text-xs">All FG required on</span><input id="fgRequiredDate" type="date" value="<?= date("Y-m-d") ?>" name="fgRequiredDate" class="form-control col-md-3"></div>
                                        </div>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th class="borderNone">Sl</th>
                                                    <th class="borderNone">Item Code</th>
                                                    <th class="borderNone">Item Title</th>
                                                    <th class="borderNone">Total Consumption</th>
                                                    <th class="borderNone">Extra Required</th>
                                                    <th class="borderNone">UOM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($previewMrpObj["bomItems"] as $listRow) {
                                                    if ($listRow["goodsType"] != 3) {
                                                        continue;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $sl += 1 ?></td>
                                                        <td><?= $listRow["itemCode"] ?></td>
                                                        <td><?= $listRow["itemName"] ?></td>
                                                        <td><?= number_format($listRow["totalConsumption"], 2) ?></td>
                                                        <td><?= number_format($listRow["extraRequired"] ?? 0, 2) ?></td>
                                                        <td><?= ucfirst($listRow["uom"]) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>

                                        <div class="row p-0 m-1">
                                            <div class="col-6 pl-0">SFG Order List</div>
                                        </div>
                                        <table class="table  table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>Reference</th>
                                                    <th class="borderNone">Item</th>
                                                    <th class="borderNone">Expected Date</th>
                                                    <th class="borderNone">Order qty</th>
                                                    <th class="borderNone">Work Center</th>
                                                    <th class="borderNone">Table</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($previewMrpObj["bomItems"] as $listRow) {
                                                    if ($listRow["goodsType"] != 2) {
                                                        continue;
                                                    }
                                                     //    console($listRow['extraRequired']);
                                                     $extraRequired = $listRow['extraRequired'];
                                                    $rand = rand(100, 1000);
                                                ?>


                                                    <tr id="sfg_prod" class="sfg_prod sfg_prod_<?= $rand ?>">
                                                        <input class="form-control actual_itemQty actual_itemQty_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][actual_itemQty]" value="<?= number_format($listRow["extraRequired"] ?? 0, 2) ?>" id="actual_itemQty" data-attr="<?= $rand ?>">

                                                        <td><input class="form-control refNo refNo_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][refNo]" value="<?= $main_fg['porCode'] ?>" id="refNo" data-attr="<?= $rand ?>"><?= $main_fg['porCode'] ?></td>
                                                        <td class="pre-normal">
                                                         
                                                          <?= $listRow["itemName"] . '(' . $listRow["itemCode"] . ')' ?>


                                                            <input class="form-control itemCode itemCode_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][itemCode]" value="<?= $listRow['itemCode'] ?>" id="itemCode" data-attr="<?= $rand ?>">
                                                            <input class="form-control itemId itemId_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][itemId]" value="<?= $listRow["item_id"] ?>" id="itemId" data-attr="<?= $rand ?>">

                                                        </td>
                                                        <td><input class="form-control expDate expDate_<?= $rand ?>" type="date" name="listItem[<?= $rand ?>][expDate]" value="" id="expDate" data-attr="<?= $rand ?>"></td>
                                                        <td><input class="form-control itemQty itemQty_<?= $rand ?>" type="number" name="listItem[<?= $rand ?>][itemQty]" value="<?= $extraRequired ?>" id="itemQty" data-attr="<?= $rand ?>"></td>
                                                        <td> <select name="listItem[<?= $rand ?>][wcId]" id="wcDropdown" class="form-control selct-wc-dropdown wcDropdown wcDropdown_<?= $rand ?>" data-attr="<?= $rand ?>">
                                                                <option value="">Select Work Center</option>
                                                                <?php
                                                                $select_wc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);
                                                                foreach ($select_wc['data'] as $wc) {
                                                                ?>

                                                                    <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . '(' . $wc['work_center_name'] . ')' ?></option>

                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="listItem[<?= $rand ?>][tableId]" id="TableDropdown" class="form-control selct-Table-dropdown TableDropdown TableDropdown_<?= $rand ?>">
                                                                <option value="">Select Table</option>

                                                            </select>
                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>
                                            </tbody>






                                        </table>


                                        <div class="row p-0 m-1">
                                            <div class="col-6 pl-0">FG Order List</div>
                                        </div>
                                        <table class="table  table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>Reference</th>
                                                    <th class="borderNone">Item</th>
                                                    <th class="borderNone">Expected Date</th>
                                                    <th class="borderNone">Order qty</th>
                                                    <th class="borderNone">Work Center</th>
                                                    <th class="borderNone">Table</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($previewMrpObj["bomItems"] as $listRow) {
                                                    if ($listRow["goodsType"] != 3) {
                                                        continue;
                                                    }
                                                    //console($listRow);
                                                    $rand = rand(1001, 10000);
                                                ?>


                                                    <tr id="sfg_prod" class="sfg_prod sfg_prod_<?= $rand ?>">
                                                        <input class="form-control actual_itemQty actual_itemQty_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][actual_itemQty]" value="<?= number_format($listRow["extraRequired"] ?? 0, 2) ?>" id="actual_itemQty" data-attr="<?= $rand ?>">
                                                        <td><input class="form-control refNo refNo_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][refNo]" value="<?= $main_fg['porCode'] ?>" id="refNo" data-attr="<?= $rand ?>"><?= $main_fg['porCode'] ?></td>

                                                        <td><?= $listRow["itemName"] . '(' . $listRow["itemCode"] . ')' ?>
                                                            <input class="form-control itemCode itemCode_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][itemCode]" value="<?= $listRow['itemCode'] ?>" id="itemCode" data-attr="<?= $rand ?>">
                                                            <input class="form-control itemId itemId_<?= $rand ?>" type="hidden" name="listItem[<?= $rand ?>][itemId]" value="<?= $listRow["item_id"] ?>" id="itemId" data-attr="<?= $rand ?>">
                                                        </td>
                                                        <td><input class="form-control expDate expDate_<?= $rand ?>" type="date" name="listItem[<?= $rand ?>][expDate]" value="" id="expDate" data-attr="<?= $rand ?>"></td>
                                                        <td><input class="form-control itemQty itemQty_<?= $rand ?>" type="number" name="listItem[<?= $rand ?>][itemQty]" value="<?= number_format($listRow["extraRequired"] ?? 0, 2) ?>" id="itemQty" data-attr="<?= $rand ?>"></td>
                                                        <td> <select name="listItem[<?= $rand ?>][wcId]" id="wcDropdown" class="form-control selct-wc-dropdown wcDropdown wcDropdown_<?= $rand ?>" data-attr="<?= $rand ?>">
                                                                <option value="">Select Work Center</option>
                                                                <?php
                                                                $select_wc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);
                                                                foreach ($select_wc['data'] as $wc) {
                                                                ?>

                                                                    <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . '(' . $wc['work_center_name'] . ')' ?></option>

                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="listItem[<?= $random ?>][tableId]" id="TableDropdown" class="form-control selct-Table-dropdown TableDropdown TableDropdown_<?= $rand ?>">
                                                                <option value="">Select Table</option>

                                                            </select>
                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>
                                            </tbody>

                                            <tbody>
                                                <?php
                                                $random = rand(10, 100);
                                                ?>


                                                <tr id="sfg_prod" class="sfg_prod sfg_prod_<?= $random ?>">
                                                    <input class="form-control actual_itemQty actual_itemQty_<?= $random ?>" type="hidden" name="listItem[<?= $random ?>][actual_itemQty]" value="<?= number_format($main_fg['qty'] ?? 0, 2) ?>" id="actual_itemQty" data-attr="<?= $random ?>">

                                                    <td><input class="form-control refNo refNo_<?= $random ?>" type="hidden" name="listItem[<?= $random ?>][refNo]" value="<?= $main_fg['porCode'] ?>" id="refNo" data-attr="<?= $random ?>"><?= $main_fg['porCode'] ?></td>


                                                    <td><?= $main_fg["itemName"] . '(' . $main_fg["itemCode"] . ')' ?>
                                                        <input class="form-control itemCode itemCode_<?= $random ?>" type="hidden" name="listItem[<?= $random ?>][itemCode]" value="<?= $main_fg["itemCode"] ?>" id="itemCode" data-attr="<?= $random ?>">
                                                        <input class="form-control itemId itemId_<?= $random ?>" type="hidden" name="listItem[<?= $random ?>][itemId]" value="<?= $main_fg["itemId"] ?>" id="itemId" data-attr="<?= $random ?>">
                                                    </td>
                                                    <td><input class="form-control expDate expDate_<?= $random ?>" type="date" name="listItem[<?= $random ?>][expDate]" value="" id="expDate" data-attr="<?= $random ?>"></td>
                                                    <td><input class="form-control itemQty itemQty_<?= $random ?> itemQtyid" type="number" name="listItem[<?= $random ?>][itemQty]" value="<?= number_format($main_fg["qty"] ?? 0, 2) ?>" id="itemQty" data-attr="<?= $random ?>"></td>
                                                    <td> <select name="listItem[<?= $random ?>][wcId]" id="wcDropdown" class="form-control selct-wc-dropdown wcDropdown wcDropdown_<?= $random ?>" data-attr="<?= $random ?>">
                                                            <option value="">Select Work Center</option>
                                                            <?php
                                                            $select_wc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);
                                                            foreach ($select_wc['data'] as $wc) {
                                                            ?>

                                                                <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . '(' . $wc['work_center_name'] . ')' ?></option>

                                                            <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="listItem[<?= $random ?>][tableId]" id="TableDropdown" class="form-control selct-Table-dropdown TableDropdown TableDropdown_<?= $random ?>">
                                                            <option value="">Select Table</option>

                                                        </select>
                                                    </td>
                                                </tr>

                                            </tbody>

                                        </table>



                                        <div class="m-2 mt-4 text-right">
                                            <input type="hidden" name="releaseOrderData" value="<?= $_GET["run-mrp"] ?>">
                                            <button id="confirmAndReleaseOrderBtn" class="btn btn-primary" type="submit" name="confirmAndReleaseOrderBtn">Confirm & Release Order</button>
                                        </div>


                                <?php
                                    } else {
                                        echo "<p>MRP Preview generation failed or Bom items not found!</p>";
                                    }
                                }
                                ?>
                            </form>

                        </div>
                    </div>
                </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        $(".wcDropdown").on("change", function() {
            // alert(1);
            var attr = $(this).data('attr');
            // alert(attr);
            var wc_id = $(".wcDropdown_" + attr).val();
            // alert(wc_id);
            $.ajax({
                type: "GET",
                url: `bom/ajax/ajax-table.php`,
                data: {
                    wc_id

                },
                beforeSend: function() {
                    $(".TableDropdown_" + attr).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    //  alert(response);
                    $(".TableDropdown_" + attr).html(response);
                }
            });


        });



        $(document).on("keyup keydown paste", ".sfg_prod .itemQty", function() {
            // alert(1);

            var val = $(this).val();
            var attr = $(this).data('attr');
            var qty = $('.actual_itemQty_' + attr).val();

            console.log(qty+'-total Qty');

            var totalSum = 0;
            $('.sfg_prod_' + attr).find('.itemQty_' + attr).each(function() {
                totalSum += Number($(this).val());
            });

            if (Number(totalSum) < Number(qty)) {
                // Clone the existing tr element
                clone(attr, qty, val);
            } else {
                // alert(1);
            }

        });
        function clone(attr, qty, val) {
            var clonedTr = $('.sfg_prod_' + attr + '_cloned');

            if (clonedTr.length) {
                // Update values in the existing cloned tr
                clonedTr.find('.itemQty_' + attr).val(Number(qty) - Number(val));
            } else {
                // Clone the existing tr element
                clonedTr = $('.sfg_prod_' + attr).clone();

                // Update values in the cloned tr
                clonedTr.find('.itemQty_' + attr).val(Number(qty) - Number(val));

                // Add a class to identify the cloned row
                clonedTr.addClass('sfg_prod_' + attr + '_cloned');

                // Append the cloned tr after the original one
                $('.sfg_prod_' + attr).after(clonedTr);
            }
        }






    });
</script>