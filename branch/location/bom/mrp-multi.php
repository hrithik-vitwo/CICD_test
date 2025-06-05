<?php
$productionOrderIdArr = isset($_GET["run-multi-mrp"]) ? explode(",", base64_decode($_GET["run-multi-mrp"])) : [];
$productionOrderId = $productionOrderIdArr[0];
$workCenterListObj = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);
?>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
    .is-production-order .left-item-modal .modal-dialog .modal-content {
        border-radius: 15px;
    }

    .is-production-order .left-item-modal .modal-dialog .modal-content .modal-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .is-production-order .left-item-modal .modal-dialog .modal-content .modal-body {
        max-height: 550px;
        height: auto;
    }

    .is-production-order .card.produceable-card {
        overflow: auto;
        border-radius: 0;
    }
</style>


<div class="content-wrapper is-production-order is-run-mrp">
    <section class="content">
        <div class="container-fluid">
            <?php
                // console(base64_decode($_GET["run-mrp"]));
            ?>
            <form action="" method="post" id="mrpPreviewForm">
                <div class="d-flex">
                    <span class="h5 font-weight-bold">Multi-MRP Preview</span>
                    <div class="d-flex ml-auto">
                        <div class="btn-group btn-group-toggle col-2 pr-0" data-toggle="buttons">
                            <label class="btn btn-secondary active waves-effect waves-light">
                                <input type="radio" class="expand_collapse" id="collapser" name="expand_collapse" value="collapse" autocomplete="off">Collapse
                            </label>
                            <label class="btn btn-secondary waves-effect waves-light">
                                <input type="radio" class="expand_collapse" id="expander" name="expand_collapse" value="expand" autocomplete="off" checked="">Expand
                            </label>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="productionOrderId" value="<?= $productionOrderId ?>">
                <input type="hidden" name="submitProductionOrderMrpReleaseFrm" value="<?= $productionOrderId ?>">
                <table id="basic" class="table">
                    <thead>
                        <tr>
                            <th>Material Details</th>
                            <th>Item Code</th>
                            <th>Production Order</th>
                            <th>Consumption</th>
                            <th>Total Qty</th>
                            <th>UOM</th>
                        </tr>
                    </thead>
                    <tbody id="treeTableBody">
                        <!-- <tr data-node-id="1">
                            <td>Kuytrdfg</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1" data-node-pid="1">
                            <td><a href="#">1.1</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1.1" data-node-pid="1.1">
                            <td><a href="#">1.1.1</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1.2" data-node-pid="1.1">
                            <td><a href="#">1.1.2</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.2" data-node-pid="1">
                            <td><a href="#">1.2</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr> -->
                    </tbody>
                </table>

                <span class="h5 font-weight-bold">Produceable Items</span>
                <div class="card produceable-card">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Production Order</th>
                                    <th>Consumption Rate</th>
                                    <th>Available Qty</th>
                                    <th>Required Qty</th>
                                    <th>UOM</th>
                                    <th>Prod Qty</th>
                                    <th>Work Center</th>
                                    <th>Table</th>
                                    <th>Expected Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="consumeableItems">
                            </tbody>
                        </table>
                    </div>
                </div>


                <span class="h5 font-weight-bold">Purchasable Items</span>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Production Order</th>
                            <th>Available Stock</th>
                            <th>Total Required</th>
                            <th>PR Qty</th>
                            <th>UOM</th>
                            <th>Purchase Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="nonConsumeableItems">
                    </tbody>
                </table>
                <button class="btn btn-primary" type="submit" name="submitReleaseOrder" value="submitReleaseOrder" id="btnReleaseOrder">Release Order</button>
            </form>
        </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        const initTreeTable = () => {
            $('#basic').simpleTreeTable({
                expander: $('#expander'),
                collapser: $('#collapser'),
                store: 'session',
                storeKey: 'simple-tree-table-basic'
            });
        }
        initTreeTable();
        // ================================[      CUSTOM JS START     ]========================================
        console.log("Welcome to MRP");
        let wcTablePendingList = [];
        let productionOrderWcId = 0;

        let TreeTableJSON = null;
        $.ajax({
            url: '<?= BASE_URL ?>branch/location/bom/ajax/get-multi-mrp-preview.php?production-order-id-arr=<?= $_GET["run-multi-mrp"] ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Render the tree when the data is successfully fetched
                console.log("Calling rander function!");
                console.log(data);
                // let productionOrderDetails = data.productionOrder;
                // productionOrderWcId = productionOrderDetails.work_center;
                let treeTableBodyHtml = renderTreeTable(data.data["productionOrderBomTree"]);
                $("#treeTableBody").html(`${treeTableBodyHtml}`);

                initTreeTable();

                $(`#consumeableItems`).html(Object.keys(data.data.produceableItems).map((keyName, index) => {
                    let item = data.data.produceableItems[keyName];
                    return renderProduceableItemTableRow(item, index);
                }).join(""));

                // $(`#nonConsumeableItems`).html(data.data.purchasableItems.map((item, index) => {
                $(`#nonConsumeableItems`).html(Object.keys(data.data.purchasableItems).map((keyName, index) => {
                    let item = data.data.purchasableItems[keyName];
                    return renderPurchasableItemTableRow(item, index);
                }).join(""));

                // // adding options for pending work center tables after all the row genereted.
                // wcTablePendingList.map((rowItem) => {
                //     loadTheWcTable(productionOrderWcId, rowItem.wc_index, rowItem.wc_subindex);
                // });
            },
            error: function(error) {
                console.error('Error fetching data:', error);
            }
        });

        let splitOrderCounter = 0;

        function renderProduceableItemTableRow(item, index, isSplited = false) {
            splitOrderCounter += isSplited ? 1 : 0;
            let subIndex = isSplited ? splitOrderCounter : 0;
            let itemId = item.itemId ? item.itemId : item.item_id;
            let prodQty = isSplited ? 0 : item.extraRequiredQty;
            let cellColorClass = index % 2 == 0 ? 'bg-light' : '';
            let wcList = JSON.parse(`<?= json_encode($workCenterListObj['data'] ?? []) ?>`) ?? [];
            let wc_id = item.work_center;
            wcTablePendingList.push({
                wc_index: index,
                wc_subindex: subIndex
            });
            return `<tr>
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemId]" value="${itemId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][productionOrderId]" value="${item.productionOrderId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][productionOrderCode]" value="${item.productionOrderCode}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][requiredQty]" value="${item.extraRequiredQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][goodsType]" value="${item.goodsType}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][uomId]" value="${item.uomId ? item.uomId : 0}">
                        <td class="${cellColorClass}">${item.itemCode}</td>
                        <td class="${cellColorClass}">
                        <p class="pre-normal">${item.itemName}</p>
                        </td>
                        <td class="${cellColorClass}">${item.productionOrderCode}</td>
                        <td class="${cellColorClass} text-right">${item.consumptionRate}</td>
                        <td class="${cellColorClass} text-right">${item.availableQty}</td>
                        <td class="${cellColorClass} text-right">${item.extraRequiredQty}</td>
                        <td class="${cellColorClass} text-right">${item.uom}</td>
                        <td class="${cellColorClass}">
                            <input type="number" step="any" name="consumeableItems[${index}][${subIndex}][prodQty]" class="form-control inputProdQty" data-item_index="${index}" data-item_id="${itemId}" data-parent_id="${item.parentId}" data-required_qty="${item.extraRequiredQty}" value="${prodQty}" required>
                            <span class="pre-normal text-xs spanProdQtyInputMsg spanProdQtyInputMsg_${index}"></span>
                        </td>
                        <td class="${cellColorClass}">
                          <select id="wcDropdown" name="consumeableItems[${index}][${subIndex}][work_center]" class="form-control selct-wc-dropdown wcDropdown wcDropdown_${index} ?>" data-wc_index="${index}" data-wc_subindex="${subIndex}" required>
                            <option value="">Select Work Center</option>
                            ${wcList.map((wc, index)=>{
                                let selectText = wc['work_center_id'] == productionOrderWcId ? 'selected' : '';
                                //console.log(`wc_id: ${wc_id}, wc['work_center_id']: ${wc['work_center_id']}, selectText: ${selectText}`);
                                return `<option value="${wc['work_center_id']}" ${selectText}>${wc['work_center_code']} (${wc['work_center_name']})</option>`;
                            }).join("")}
                          </select>
                        </td>
                        <td class="${cellColorClass}">
                          <select name="consumeableItems[${index}][${subIndex}][table_map]" class="form-control selct-Table-dropdown TableDropdown" id="tableDropdown_${index}_${subIndex}" required>
                            <option value="">Select Table</option>
                          </select>
                        </td>
                        <td class="${cellColorClass}">
                            <input type="date" name="consumeableItems[${index}][${subIndex}][expected_date]" class="form-control" required>
                        </td>
                        <td class="${cellColorClass}">
                            ${ isSplited ? (
                                `<span class="btn btn-sm btn-warning removeSplitOrderBtn" data-item_id="${itemId}" data-parent_id="${item.parentId}" id="removeSplitOrderBtn_${itemId}"> - </span>`
                            ):(
                                `<span class="btn btn-sm btn-success splitOrderBtn" data-item_id="${itemId}" data-parent_id="${item.parentId}" data-item_row_index="${index}" data-item_json_data="${btoa(JSON.stringify(item))}" id="splitOrderBtn_${itemId}"> + </span>`
                            )}
                        </td>
                    </tr>`;
        }

        function renderPurchasableItemTableRow(item, index) {
            return `<tr>
                        <input type="hidden" name="purchasableItems[${index}][itemId]" value="${item.itemId}">
                        <input type="hidden" name="purchasableItems[${index}][productionOrderId]" value="${item.productionOrderId}">
                        <input type="hidden" name="purchasableItems[${index}][productionOrderCode]" value="${item.productionOrderCode}">
                        <input type="hidden" name="purchasableItems[${index}][requiredQty]" value="${item.extraRequiredQty}">
                        <input type="hidden" name="purchasableItems[${index}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="purchasableItems[${index}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="purchasableItems[${index}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="purchasableItems[${index}][itemName]" value="${item.itemName}">
                        <input type="hidden" name="purchasableItems[${index}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="purchasableItems[${index}][goodsType]" value="${item.goodsType}">
                        <input type="hidden" name="purchasableItems[${index}][uomId]" value="${item.uomId}">
                        <td>${item.itemCode}</td>
                        <td><p class="pre-normal">${item.itemName}</p></td>
                        <td><p class="pre-normal">${item.productionOrderCode}</p></td>
                        <td class="text-right">${item.availableQty}</td>
                        <td class="text-right">${item.totalConsumptionQty}</td>
                        <td class="text-right">${item.extraRequiredQty}
                        <input type="hidden" id="itemQty_${index}" value="${item.extraRequiredQty}">
                        </td>
                        <td>${item.uom}</td>
                        <td><input type="text" name="purchasableItems[${index}][purchaseNote]" class="form-control" placeholder="Note..."></td>
                        <td class="action-flex-btn"> 
                         <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_${index}">
                            <i class="fa fa-cog statusItemBtn" id="statusItemBtn_[${index}][itemId] ?>"></i>
                         </button>


                         <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_${index}" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-white">Delivery Schedule</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_${index}">
                                                    <div class="row">
                                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                            <div class="form-input">
                                                                <label>Expected date</label>
                                                                <input type="date"
                                                                    name="listItem[${index}][deliverySchedule][${index}][multiDeliveryDate]"
                                                                    class="form-control delDate delDate_${index} ?>" data-attr="${index}"
                                                                    id="delivery-date" placeholder="delivery date" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                            <div class="form-input">
                                                                <label>Quantity</label>
                                                                <input type="text" name="listItem[${index}][deliverySchedule][${index}][quantity]"
                                                                    class="form-control multiQuantity multiQty_${index}" data-attr="${index}"
                                                                    id="multiQuantity_${index}" placeholder="quantity" value="${item.extraRequiredQty}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                            <div class="add-btn-plus">
                                                                <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light"
                                                                    onclick='addDeliveryQty(${index})'>
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-fixed">
                                            <button type="submit" id="finalBtn_${index}"
                                                class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light"
                                                data-dismiss="modal" aria-label="Close">Save & Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </td>
                    </tr>`;
        }



        function renderTreeTable(nodes, parentQty=1) {
            // console.log(nodes);
            let html = '';
            nodes.forEach(function(node, index) {
                let nodeId = `${node.productionOrderId}_${node.itemId}`;
                let parentNodeId = node.itemId==node.parentId ? "" : `${node.productionOrderId}_${node.parentId}`;
                let parentQuantity = node.qty ? parseFloat(node.qty) : parentQty;
                let totalConsumption = parseFloat(node.consumptionRate)*parentQuantity;
                html += `
                    <tr data-node-id="${nodeId}" data-node-pid="${parentNodeId}">
                        <td><span class="pre-normal">${node.itemName.toUpperCase()}</span></td>
                        <td><span class="pre-normal">${node.itemCode}</span></td>
                        <td><span class="pre-normal">${node.productionOrderCode}</span></td>
                        <td class="text-right">${node.consumptionRate}</td>
                        <td class="text-right">${totalConsumption}</td>
                        <td class="text-right">${node.uom}</td>
                    </tr>`;
                if (node.childrens) {
                    html += renderTreeTable(node.childrens, totalConsumption);
                }
            });
            return html;
        }

        $(document).on("click", ".splitOrderBtn", function() {
            let itemId = $(this).data("item_id");
            let parentId = $(this).data("parent_id");
            let itemIndex = $(this).data("item_row_index");
            let targetRow = $(this).closest("tr");
            let itemRawData = $(this).data("item_json_data");
            let itemData = JSON.parse(atob(itemRawData));
            targetRow.after(renderProduceableItemTableRow(itemData, itemIndex, true));
        });

        $(document).on("click", ".removeSplitOrderBtn", function() {
            let itemId = $(this).data("item_id");
            let parentId = $(this).data("parent_id");
            let targetRow = $(this).closest("tr");
            targetRow.remove();
        });

        function loadTheWcTable(wc_id, wc_index, wc_subindex) {
            $.ajax({
                type: "GET",
                url: `bom/ajax/ajax-table.php`,
                data: {
                    wc_id
                },
                beforeSend: function() {
                    $(`#tableDropdown_${wc_index}_${wc_subindex}`).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $(`#tableDropdown_${wc_index}_${wc_subindex}`).html(`<option value="">Select Table</option>${response}`);
                }
            });
        }

        $(document).on("change", ".wcDropdown", function() {
            let wc_index = $(this).data('wc_index');
            let wc_subindex = $(this).data('wc_subindex');
            let attr = $(this).data('attr');
            let wc_id = $(this).val();
            loadTheWcTable(wc_id, wc_index, wc_subindex);
        });

        $(document).on('keyup', '.inputProdQty', function() {
            let item_id = $(this).data('item_id');
            let parent_id = $(this).data('parent_id');
            let item_index = $(this).data('item_index');
            let sum = 0;
            let required_qty = parseFloat($(this).data("required_qty"));
            $(`.spanProdQtyInputMsg_${item_index}`).html(``);
            $('.inputProdQty').each(function() {
                if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
                    sum += parseFloat($(this).val()) || 0;
                }
            });

            $('.inputProdQty').each(function() {
                if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
                    if (sum != required_qty) {
                        let rem = required_qty - sum;
                        $(`.spanProdQtyInputMsg_${item_index}`).html(`<span class="text-danger">Combined quantity is ${rem>0 ? 'lesser': 'greater'} than required quantity by ${Math.abs(rem)}</span>`);
                    }
                }
            });
            checkFullFormDataIsValid();
        });

        function checkFullFormDataIsValid() {
            let isValid = true;
            let prodQtyList = {};
            let requiredQtyList = {};
            $(".inputProdQty").each(function() {
                let item_id = $(this).data("item_id");
                let parent_id = $(this).data("parent_id");
                let required_qty = parseFloat($(this).data("required_qty"));
                let inputProdQty = parseFloat($(this).val());
                let uniqueItemId = `${item_id}_${parent_id}`;
                requiredQtyList[uniqueItemId] = required_qty;
                let newProdQty = prodQtyList[uniqueItemId] > 0 ? prodQtyList[uniqueItemId] + inputProdQty : inputProdQty;
                prodQtyList[uniqueItemId] = newProdQty;
                if (newProdQty == 0) {
                    isValid = false;
                }
            });

            Object.keys(requiredQtyList).forEach(function(key, index) {
                if (requiredQtyList[key] != prodQtyList[key]) {
                    isValid = false;
                }
            });

            if (!isValid) {
                $(`#btnReleaseOrder`).attr('disabled', true);
            } else {
                $(`#btnReleaseOrder`).attr('disabled', false);
            }
            return isValid;
        }


        $(document).on("submit", "#mrpPreviewForm", function(e) {
            if (!checkFullFormDataIsValid()) {
                e.preventDefault();
                $(`#btnReleaseOrder`).attr('disabled', true);
                alert("Invalid Produceable Quantity provided!");
            } else {
                $(`#btnReleaseOrder`).attr('disabled', true);
            }
        });

    });

    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });

    function addDeliveryQty(randCode) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity multiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger qty_minus" data-attr="${randCode}">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
    }

    $(document).on("click", ".qty_minus", function(e) {
        let element = $(this)[0].getAttribute("data-attr");
        setTimeout(function() {
            let sumQty = 0;
            for (elem of $(`.multiQty_${element}`)) {
                sumQty += Number($(elem).val());
            };
            let actualQty = Number($(`#itemQty_${element}`).val());
            if (actualQty === sumQty) {
                $(`#finalBtn_${element}`)[0].disabled = false;
                $(".po_qtyValidation").remove();
            } else {
                $(`#finalBtn_${element}`)[0].disabled = true;
                $(`.modal-body`).append('<span class="error po_qtyValidation">Quantity is not matching.</span>');
                $(".po_qtyValidation").show();
            };
        }, 100);

    });

    $(document).on("change", ".itemQty", function(e) {
        let element = $(this)[0].getAttribute("id").split("_")[1];
        $(`#multiQuantity_${element}`).val($(this).val());
    });

    $(document).on("keyup", ".multiQuantity", function(e) {
        let element = $(this)[0].getAttribute("data-attr");
        let sumQty = 0;
        for (elem of $(`.multiQty_${element}`)) {
            sumQty += Number($(elem).val());
        };
        let actualQty = Number($(`#itemQty_${element}`).val());
        // alert(actualQty);
        if (actualQty === sumQty) {
            $(`#finalBtn_${element}`)[0].disabled = false;
            $(".po_qtyValidation").remove();
        } else {
            $(`#finalBtn_${element}`)[0].disabled = true;
            $(`.modal-body`).append('<span class="error po_qtyValidation">Quantity is not matching.</span>');
            $(".po_qtyValidation").show();
        };
    });
</script>