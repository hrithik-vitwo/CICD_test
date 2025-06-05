<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/common/func-gl-summary-new.php");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid" style="overflow: auto;">
      <?php
      console($_POST);
      ?>
      <form action="" method="post" id="mrpPreviewForm">
        <div class="d-flex">
          <span class="h5 font-weight-bold">MRP Preview</span>
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
        <table id="basic" class="table">
          <thead>
            <tr>
              <th>Material Details</th>
              <th>Item Code</th>
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
        <table class="table">
          <thead>
            <tr>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Consumption Rate</th>
              <th>Available Qty</th>
              <th>Required Qty</th>
              <th>UOM</th>
              <th>Prod Qty</th>
              <th>Work Center</th>
              <th>Table</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="consumeableItems">
          </tbody>
        </table>
        <span class="h5 font-weight-bold">Purchasable Items</span>
        <table class="table">
          <thead>
            <tr>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Available Qty</th>
              <th>Required Qty</th>
              <th>UOM</th>
            </tr>
          </thead>
          <tbody id="nonConsumeableItems">
          </tbody>
        </table>
        <button class="btn btn-primary" type="submit">Confirm MRP</button>
      </form>
    </div>
  </section>
</div>
<?php
require_once("../common/footer.php");

$workCenterListObj = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);

?>
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
    let TreeTableJSON = null;
    $.ajax({
      url: 'https://www.devalpha.vitwo.ai/branch/location/bom/ajax/get-mrp-preview.php?production-order-id=87',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        // Render the tree when the data is successfully fetched
        console.log("Calling rander function!");
        console.log(data);
        //console.log(data["productionOrder"]["remainQty"]);
        let productionOrderDetails = data.productionOrder;
        let treeTableBodyHtml = renderTreeTable(data["bomDetails"], parseFloat(data["productionOrder"]["remainQty"]), "0_0");
        $("#treeTableBody").html(`
                    <tr data-node-id="0_0">
                        <td><span class="pre-normal">${productionOrderDetails.itemName.toUpperCase()}</span></td>
                        <td><span class="pre-normal">${productionOrderDetails.itemCode}</span></td>
                        <td class="text-right">-</td>
                        <td class="text-right">${productionOrderDetails.remainQty}</td>
                        <td class="text-right">${productionOrderDetails.uom}</td>
                    </tr>${treeTableBodyHtml}`);
        initTreeTable();

        $(`#consumeableItems`).html(data.produceableItems.map((item, index) => {
          return renderProduceableItemTableRow(item, index);
        }).join(""));
        $(`#nonConsumeableItems`).html(data.notProduceableItems.map((item, index) => {
          return renderPurchasableItemTableRow(item, index);
        }).join(""));
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
      let prodQty = isSplited ? 0 : item.requiredQty;
      return `
                    <tr>
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemId]" value="${itemId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][requiredQty]" value="${item.requiredQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][goodsType]" value="${item.goodsType}">
                        <td>${item.itemCode}</td>
                        <td><p class="pre-normal">${item.itemName}</p></td>
                        <td class="text-right">${item.consumptionRate}</td>
                        <td class="text-right">${item.availableQty}</td>
                        <td class="text-right">${item.requiredQty}</td>
                        <td>${item.uom}</td>
                        <td>
                            <input type="text" name="consumeableItems[${index}][${subIndex}][prodQty]" class="form-control inputProdQty" data-item_id="${itemId}" data-parent_id="${item.parentId}" data-required_qty="${item.requiredQty}" value="${prodQty}" required></td>
                        <td>
                          <select id="wcDropdown" name="consumeableItems[${index}][${subIndex}][work_center]" class="form-control selct-wc-dropdown wcDropdown wcDropdown_${index} ?>" data-wc_index="${index}" data-wc_subindex="${subIndex}" required>
                            <option value="">Select Work Center</option>
                            <?php
                            foreach ($workCenterListObj['data'] as $wc) {
                            ?>
                                <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . '(' . $wc['work_center_name'] . ')' ?></option>
                              <?php
                            }
                              ?>
                          </select>
                        </td>
                        <td>
                          <select name="consumeableItems[${index}][${subIndex}][table_map]" class="form-control selct-Table-dropdown TableDropdown" id="tableDropdown_${index}_${subIndex}" required>
                            <option value="">Select Table</option>
                          </select>
                        </td>
                        <td>
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
                        <input type="hidden" name="purchasableItems[${index}][itemId]" value="${item.item_id}">
                        <input type="hidden" name="purchasableItems[${index}][requiredQty]" value="${item.requiredQty}">
                        <input type="hidden" name="purchasableItems[${index}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="purchasableItems[${index}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="purchasableItems[${index}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="purchasableItems[${index}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="purchasableItems[${index}][goodsType]" value="${item.goodsType}">
                        <td>${item.itemCode}</td>
                        <td><p class="pre-normal">${item.itemName}</p></td>
                        <td class="text-right">${item.availableQty}</td>
                        <td class="text-right">${item.requiredQty}</td>
                        <td>${item.uom}</td>
                    </tr>`;
    }



    function renderTreeTable(nodes, parentQty = 1, parentNodeId = null) {
      let html = '';
      nodes.forEach(function(node, index) {
        let nodeId = `${node.bom_id}_${node.item_id}`;
        html += `
                    <tr data-node-id="${nodeId}" ${ parentNodeId ? `data-node-pid="${parentNodeId}"` : ""}>
                        <td><span class="pre-normal">${node.itemName.toUpperCase()}</span></td>
                        <td><span class="pre-normal">${node.itemCode}</span></td>
                        <td class="text-right">${node.consumptionRate}</td>
                        <td class="text-right">${node.consumptionRate*parentQty}</td>
                        <td class="text-right">${node.uom}</td>
                    </tr>`;
        if (node.childrens) {
          html += renderTreeTable(node.childrens, node.consumptionRate * parentQty, nodeId);
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

    $(document).on("change", ".wcDropdown", function() {
      let wc_index = $(this).data('wc_index');
      let wc_subindex = $(this).data('wc_subindex');
      let attr = $(this).data('attr');
      let wc_id = $(this).val();
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
          $(`#tableDropdown_${wc_index}_${wc_subindex}`).html(response);
        }
      });
    });


    // $(document).on("submit", "#mrpPreviewForm", function(e) {
    //     let isValid = true;
    //     let qtyList = {};
    //     $(".inputProdQty").each(function(){
    //         let item_id = $(this).data("item_id");
    //         let parent_id = $(this).data("parent_id");
    //         let required_qty = parseFloat($(this).data("required_qty"));
    //         let inputProdQty = parseFloat($(this).val());
    //         if(inputProdQty==0){
    //             isValid = false;
    //             console.log("Zero error", inputProdQty);
    //         }
    //         qtyList[`${item_id}_${parent_id}`] ? qtyList[`${item_id}_${parent_id}`]+inputProdQty : qtyList[`${item_id}_${parent_id}`] = 0;
    //         if(qtyList[`${item_id}_${parent_id}`]!=required_qty){
    //             isValid = false;
    //             console.log(qtyList[`${item_id}_${parent_id}`]);
    //         }
    //         console.log(`item_id: ${item_id}, parent_id: ${parent_id}, required_qty: ${required_qty}, inputProdQty: ${inputProdQty}`);
    //     });
    //     if(!isValid){
    //         e.preventDefault();
    //         alert("Invalid Produceable Quantity provided!");
    //         console.log("Validation Error");
    //     }
    // });


    $(document).on('keyup', '.inputProdQty', function() {
      var item_id = $(this).data('item_id');
      var parent_id = $(this).data('parent_id');
      var sum = 0;
      let required_qty = parseFloat($(this).data("required_qty"));
      $('.inputProdQty').each(function() {
        if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
          sum += parseFloat($(this).val()) || 0;
        }
      });

      $('.inputProdQty').each(function() {
        if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
          if(sum != required_qty){
             if(sum > required_qty){
              var rem = sum - required_qty;
            console.log("Invalid qty");
            $(this).parent().append(`<span class="text-danger">Combined quantity is greater than required quantity by `+rem+`</span>`);
             }
             else{
              var rem =  required_qty - sum;
              $(this).parent().append(`<span class="text-danger">Combined quantity is lesser than required quantity by `+rem+`</span>`);
             }
          }else{
            console.log("Valid qty!");
            $(this).parent().append(`<span class="text-success">Valid qty!</span>`);
          }
        }
      });

    //  console.log("I am working!");
    });



    // Assuming you have multiple input elements with the specified attributes
    // $('.inputProdQty').on('keyup', function() {
    //    // var sum = 0;
    // alert(1);
    //     // Iterate through each input element
    //     $('.inputProdQty').each(function() {
    //         // Check if the data-item_id and data-parent_id attributes match the desired values
    //         if ($(this).data('item_id') == 2 && $(this).data('parent_id') == 1) {
    //             // Parse the value as a number and add it to the sum
    //             sum += parseFloat($(this).val()) || 0;
    //         }
    //     });

    //     // The variable 'sum' now contains the sum of all matching input values
    //     console.log('Sum:', sum);
    // });



  });
</script>