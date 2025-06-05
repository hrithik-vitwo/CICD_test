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
        <div class="container-fluid">
            <div id="treeview"></div>
        </div>
        <div class="container-fluid" style="overflow: auto;">
            <div class="d-flex">
                <span class="h5 font-weight-bold">MRP Preview</span>
                <div class="d-flex ml-auto">
                    <!-- <button class="btn btn-sm btn-primary waves-effect waves-light" id="expander">Expand</button>
                    <button class="btn btn-sm btn-warning waves-effect waves-light ml-1" id="collapser">Collapse</button> -->
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
                        <th>Consumption Rate</th>
                        <th>Total Consumption</th>
                        <th>Avalable Qty</th>
                        <th>Required Qty</th>
                        <th>Work Center</th>
                        <th>Table</th>
                        <th>Expected Date</th>
                        <th>#</th>
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
                    </tr>
                    <tr data-node-id="1.2.1" data-node-pid="1.2">
                        <td><a href="#">1.2.1</a></td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                    </tr>
                    <tr data-node-id="1.2.2" data-node-pid="1.2">
                        <td><a href="#">1.2.2</a></td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                    </tr>
                    <tr data-node-id="2">
                        <td><a href="#">2</a></td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                    </tr>
                    <tr data-node-id="2.1" data-node-pid="2">
                        <td><a href="#">2.1</a></td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                    </tr>
                    <tr data-node-id="2.2" data-node-pid="2">
                        <td><a href="#">2.2</a></td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                        <td>text of 1</td>
                    </tr> -->
                </tbody>
            </table>
            <button class="btn btn-primary">Confirm MRP</button>
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
            // $('#open1').on('click', function() {
            //     $('#basic').data('simple-tree-table').openByID("1");
            // });
            // $('#close1').on('click', function() {
            //     $('#basic').data('simple-tree-table').closeByID("1");
            // });
        }
        initTreeTable();

        // ================================[      CUSTOM JS START     ]========================================


        console.log("Welcome to MRP");
        $.ajax({
            url: 'https://www.devalpha.vitwo.ai/branch/location/bom/ajax/get-mrp-preview.php?production-order-id=87',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Render the tree when the data is successfully fetched
                console.log("Calling rander function!");
                console.log(data);
                let treeTableBodyHtml = renderTreeTable(data["bomDetails"]);
                $("#treeTableBody").html(treeTableBodyHtml);
                initTreeTable();
                // console.log(treeTableBodyHtml);
                // let html = renderTree(data.data);
                // $("#treeview").html(html);
            },
            error: function(error) {
                console.error('Error fetching data:', error);
            }
        });

        function renderTreeTable(nodes, parentNodeId = null) {
            let html = '';
            nodes.forEach(function(node, index) {
                let nodeId = `${node.bom_id}_${node.item_id}`;
                if (node.childrens) {
                    html += `
                    <tr data-node-id="${nodeId}" ${ parentNodeId ? `data-node-pid="${parentNodeId}"` : ""}>
                        <td><span class="pre-normal">${node.itemName.toUpperCase()}</span></td>
                        <td class="text-right">${node.consumptionRate}</td>
                        <td class="text-right">${node.totalConsumption}</td>
                        <td class="text-right">${node.totalConsumption}</td>
                        <td class="text-right"><input type="number" value="${node.totalConsumption}" class="form-control text-right"></td>
                        <td class="text-center">
                            <select id="wcDropdown" class="form-control selct-wc-dropdown wcDropdown wcDropdown_${nodeId}" data-attr="${nodeId}">
                                <option value="">Select Work Center</option>
                                <?php
                                foreach ($workCenterListObj['data'] as $wc) {
                                ?>
                                    <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . ' (' . $wc['work_center_name'] . ')' ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <select id="TableDropdown" class="form-control selct-Table-dropdown TableDropdown TableDropdown_${nodeId}">
                                <option value="">Select Table</option>
                            </select>
                        </td>
                        <td class="text-center"><input type="date" class="form-control" name="" required/></td>
                        <td>${ node.totalConsumption>1 ? `<button class="btn btn-sm btn-success splitOrderBtn" id="splitOrderBtn_${nodeId}" >Split</button>`: '' }</td>
                    </tr>`;

                    html += renderTreeTable(node.childrens, nodeId);

                } else {
                    html += `
                    <tr data-node-id="${nodeId}" ${ parentNodeId ? `data-node-pid="${parentNodeId}"` : ""}>
                        <td><span class="pre-normal">${node.itemName.toUpperCase()}</span></td>
                        <td class="text-right">${node.consumptionRate}</td>
                        <td class="text-right">${node.totalConsumption}</td>
                        <td class="text-right">${node.totalConsumption}</td>
                        <td class="text-right">${node.totalConsumption}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center"><input type="date" class="form-control" name="" required/></td>
                        <td></td>
                    </tr>`;
                }
            });
            return html;
        }

        function renderTree(nodes) {
            let html = '';
            nodes.forEach(function(node) {
                html += `
                    <div class="card mt-2">
                        <div class="card-header" data-toggle="collapse" data-target="#collapse${node.id}">${node.itemName}</div>
                            <div id="collapse${node.id}" class="collapse">
                                <div class="card-body">
                                    <p><strong>ID:</strong>${node.id}, <strong>Consumption:</strong>${node.consumption} ${node.uom}, <strong>Rate:</strong>${node.rate}, <strong>Amount:</strong>${node.amount}</p>
                                    <div>
                                        ${ (node.childrens) ? renderTree(node.childrens) : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            return html;
        }

        $(document).on("click", ".splitOrderBtn", function(){
            let nodeIds = $(this).attr("id").split("_");
            let bomId = nodeIds[1];
            let itemId = nodeIds[2];
            console.log(`Spliting Order, bomId: ${bomId} & itemId: ${itemId}`);
        });

        




        $(document).on("change", ".wcDropdown", function() {
            let attr = $(this).data('attr');
            let wc_id = $(".wcDropdown_" + attr).val();
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
                    $(".TableDropdown_" + attr).html(response);
                }
            });
        });

    });
</script>