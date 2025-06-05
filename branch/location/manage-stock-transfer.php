<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");

require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/branch/func-journal.php");

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

$currentDate = date('Y-m-d');

if ($currentDate >= $min && $currentDate <= $max) {
    $max = $currentDate;
}
$goodsController = new GoodsController();

if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addNewObj = $goodsController->direct_consumption($_POST);
    if ($addNewObj['status'] == "success") {
        swalAlert($addNewObj["status"], $addNewObj['documentNo'], $addNewObj["message"], 'manage-stock-transfer.php');
    } else {
        swalAlert($addNewObj["status"], 'Warning', $addNewObj["message"]);
    }
}
?>
<style>
    .direct-create-invoice-card {
        height: auto !important;
        min-height: 100%;
        margin-bottom: 2em;
    }

    .direct-create-invoice-card .card-body {
        min-height: 100%;
        height: 330px !important;
    }

    .card.po-vendor-details-view .card-body {
        height: auto !important;
    }

    .advanced-serach .nav-action {
        flex-direction: row;
        gap: 30px;
        width: 35% !important;
    }

    .advanced-serach .form-inline {
        flex-flow: row;
    }

    div#quick-add-input span.select2.select2-container.select2-container--default {
        width: 120px !important;
    }

    .advanced-serach .form-inline select {
        width: 120px !important;
    }

    .static-currency::before,
    .dynamic-currency::before {
        bottom: 25px !important;
    }

    .so-card-body .static-currency input,
    .so-card-body .dynamic-currency input,
    .dynamic-currency select {
        height: 32px !important;
    }

    .card-body.others-info.vendor-info.so-card-body {
        height: 350px !important;
    }

    .modal.add-customer-modal .modal-dialog {
        max-width: 70%;
    }

    .modal.add-customer-modal .modal-dialog .modal-content .modal-body {
        height: 80vh;
    }

    .text-small {
        font-size: 0.8em;
    }

    .text-large {
        font-size: 1.1em;
    }

    .convertedDiv {
        display: none;
    }

    .itemDropdownDiv {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .itemDropdownDiv label {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        margin-bottom: 0;
    }

    select.order-for-select {
        width: auto !important;
    }

    .head-item-table #quick-add-input.show {
        transform: translateX(55%) !important;
    }

    .recurringDiv {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .round-off-section {
        flex-direction: column;
    }

    div#round_off_hide {
        flex-direction: column;
    }

    p.note {
        font-size: 0.65rem;
        font-weight: 600;
        margin-top: 2px;
    }

    /* .manual-accordion button {} */
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="manage-stock-transfer.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Stock Transfer List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Stock Transfer</a></li>
                <li class="back-button">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>
        </div>
        <form action="" method="POST" id="addNewSOForm">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card so-creation-card po-creation-card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Movement</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body others-info vendor-info so-card-body" style="height: 200px !important;">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">

                                    <div class="row info-form-view">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <label for="">Movement Types</label>
                                            <select name="movemenrtypesDropdown" id="movemenrtypesDropdown" class="form-control" required>
                                                <option value="storage_location" selected>Stock Transfer( S.Location to S.Location)</option>
                                                <option value="material_to_material">Stock Transfer( Material to Material)</option>
                                                <option value="production_order">Direct Consumption Posting (To Production Order)</option>
                                                <option value="cost_center">Direct Consumption Posting (To Cost Center)</option>
                                                <option value="book_to_physical">Book to Physical Posting (B2P)</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 cost-center-col">
                                            <div class="sl" id="destination">

                                                <label for="">Destination Storage Location</label>
                                                <select name="destinationStorageLocation" id="childDropdownForMovemenrtypesDropdown" class="select2 form-control" required>
                                                    <option value="">Select Storage Location</option>
                                                    <?php
                                                    $qrysrui = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
                                                    $sldattaqe = $qrysrui['data'];
                                                    // console($sldattaqe);
                                                    foreach ($sldattaqe as $datasllll) {
                                                    ?>
                                                        <option value="<?= $datasllll['storage_location_id'] . '|' . $datasllll['storageLocationTypeSlug']; ?>"><?php echo $datasllll['warehouse_code'] . ' >> ' . $datasllll['storage_location_code'] . ' >> ' . $datasllll['storage_location_name']; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <label for="date">Posting Date</label>
                                            <input type="date" name="creationDate" id="creationDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" value="<?= $max ?>" required>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div>
                                        <!-- <div class="col-lg-12 col-md-12 col-sm-12 accImpactDiv">
                                            <input type="checkbox" name="accImpact" id="accImpact" class="" checked> <i>Impact Accounting Book.</i>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card items-select-table">
                            <div class="head-item-table">
                                <div class="advanced-serach">
                                    <div class="hamburger quickadd-hamburger">
                                        <div class="wrapper-action">
                                            <i class="fa fa-plus"></i>
                                        </div>
                                    </div>
                                    <div class="nav-action quick-add-input d-flex" id="quick-add-input">
                                        <div class="itemDropdownDiv gap-2 quickAdd">
                                            <label for="">Add Item <span class="text-danger">*</span></label>
                                            <select id="itemsDropDown" class="form-control select2">
                                                <option value="">Select One</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card" id="from_table">
                                <div class="card-body" style="overflow: auto;">
                                    <table class="table table-sales-order mt-0">
                                        <thead>
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Stock</th>
                                                <th>Qty</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTable"></tbody>
                                        <span id="spanItemsTable"></span>
                                        <input type="hidden" name="" id="spanItemsdestination" value="0">
                                        <input type="hidden" name="" id="spanItemsdestinationprod" value="0">
                                    </table>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <button type="submit" name="addNewInvoiceFormSubmitBtn" id="directInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right" disabled>Submit</button>
                    </div>
                </div>
        </form>
    </section>
</div>

<div class="modal fade" id="finalSubmitModal" tabindex="-1" role="dialog" aria-labelledby="finalSubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finalSubmitModalLabel">Warning</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <!-- <span aria-hidden="true">&times;</span> -->
                </button>
            </div>
            <div class="modal-body delpreviewDetails">
                <div class="card-body">
                    <span style="color: red;
    text-align: center;
    display: flex
;
    font-size: larger;">Source and destination qty should match when UOM is the same. Proceed with different values at your own risk</span>
                    <table id="uom_table" class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Item UOM</th>
                                <th>Source Qty</th>
                                <th>Destination Qty</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="finalsubmit" id="finalsubmit" class="btn btn-primary">Final Submit</button>
            </div>
        </div>
    </div>
</div>

<?php require_once("../common/footer.php"); ?>

<script>
    $(document).on('ready', function() {
        $(document).on('change', '.select2', function() {
            $(this).select2();
        });
    });

    $(document).on("click", ".dlt-popup", function() {
        $(this).parent().parent().remove();
    });

    function rm() {
        // $(event.target).closest("tr").remove();
        $(this).parent().parent().parent().remove();
    }

    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });

    //**********************************************************************     */

    $(".accImpactDiv").hide();
    $(document).on("change", "#movemenrtypesDropdown", function() {
        var selectedValue = $(this).val();
        if (selectedValue == "book_to_physical") {
            $(".accImpactDiv").show();
        } else {
            $(".accImpactDiv").hide();
        }
        $.ajax({
            type: "GET",
            url: "ajaxs/transfer/ajax-dropdown.php?value=" + selectedValue,
            beforeSend: function() {
                $("#destination").html("Loading.....");
            },
            success: function(response) {
                let data = JSON.parse(response);
                console.log(selectedValue);
                if (selectedValue == "material_to_material") {
                    $("#from_table").html(
                        "<div class='card-body' style='overflow: auto;'><table class='table table-sales-order mt-0'><thead><tr><th>Item Code</th><th>Item Name</th><th>Stock</th><th>Qty</th><th>Destination Item</th><th>Destination Qty</th><th>Destination St. Loc.</th><th>Action</th></tr></thead><tbody id='itemsTable'></tbody><span id='spanItemsTable'></span><input type='hidden' name='' id = 'spanItemsdestination' value = '1'><input type='hidden' name='' id = 'spanItemsdestinationprod' value = '0'></table></div>");
                } else if (selectedValue == "production_order") {
                    $("#from_table").html("<div class='card-body' style='overflow: auto;'><table class='table table-sales-order mt-0'><thead><tr><th>Item Code</th><th>Item Name</th><th>Stock</th><th>Qty</th><th>Action</th></tr></thead><tbody id='itemsTable'></tbody><span id='spanItemsTable'></span><input type='hidden' name='' id = 'spanItemsdestination' value = '0'><input type='hidden' name='' id = 'spanItemsdestinationprod' value = '1'></table></div>");
                } else {
                    $("#from_table").html("<div class='card-body' style='overflow: auto;'><table class='table table-sales-order mt-0'><thead><tr><th>Item Code</th><th>Item Name</th><th>Stock</th><th>Qty</th><th>Action</th></tr></thead><tbody id='itemsTable'></tbody><span id='spanItemsTable'></span><input type='hidden' name='' id = 'spanItemsdestination' value = '0'><input type='hidden' name='' id = 'spanItemsdestinationprod' value = '0'></table></div>");
                }
                $("#destination").html(data);
                $(".select2").select2();
            }
        });


    });

    // $('.currencyDropdown')
    // .select2()
    // .on('select2:open', () => {
    //   // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    // });

    $(document).on("click", ".itemreleasetypeclass", function() {
        let itemreleasetype = $(this).val();
        var rdcode = $(this).data("rdcode");
        console.log(rdcode);
        totalquentitydiscut(rdcode);
        $("#itemSellType_" + rdcode).html(itemreleasetype);
        if (itemreleasetype == 'CUSTOM') {
            $(".customitemreleaseDiv" + rdcode).show();
            $("#itemQty_" + rdcode).prop("readonly", true);
        } else {
            $(".customitemreleaseDiv" + rdcode).hide();
            $("#itemQty_" + rdcode).prop("readonly", false);
        }
    });
    let timeoutId;

    $(document).on("keyup keydown paste", ".manualBatchNumber", function() {
        clearTimeout(timeoutId);

        // Retrieve rndcode
        let rndcode = $(this).data("rnds");
        // console.log("rndcode:", rndcode);

        // Set the HTML content to "Checking..."
        $(".manualBatchNumberDate" + rndcode).html(`Checking...`);

        // Remove any spaces from the batchNumber input
        let batchNumber = $(this).val().replace(/\s/g, '');
        // Set the input field as readonly before making the AJAX call
        $(".manualBatchNumberBornDate" + rndcode).prop('readonly', true);
        $(this).val(batchNumber); // Assuming you want to remove spaces in the displayed value



        // Set a new timeout for 20 seconds after the last keyup event
        timeoutId = setTimeout(function() {
            $.ajax({
                type: "POST",
                url: `ajaxs/transfer/ajax-batch-details.php`,
                data: {
                    act: "batchCheck",
                    batchNumber: batchNumber
                },
                beforeSend: function() {
                    $(".manualBatchNumberDate" + rndcode).html(`Checking...`);
                },
                success: function(response) {
                    // console.log(response);

                    let resData = JSON.parse(response);
                    if (resData['status'] == "success" && resData['numRows'] > 0) {
                        $(".manualBatchNumberBornDate" + rndcode).val(resData['data']['bornDate']).prop('readonly', true);
                        $(".manualBatchNumberDate" + rndcode).html(`Existing Batch.`);
                    } else {
                        $(".manualBatchNumberBornDate" + rndcode).val('').prop('readonly', false);
                        $(".manualBatchNumberDate" + rndcode).html(`Consider as New Batch.`);
                    }
                }
            });
        }, 1500); // 1.5 seconds delay
    });

    $(document).on("input keyup paste blur", ".inputQuantityClass", function() {
        let val = $(this).val();
        let base = <?= $decimalQuantity ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
            let parts = val.split(".");
            if (parts[1].length > base) {
                $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
            }
        }
    });

    $(document).on("input keyup paste blur", ".inputAmountClass", function() {
        let val = $(this).val();
        let base = <?= $decimalValue ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
            let parts = val.split(".");
            if (parts[1].length > base) {
                $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
            }
        }
    });

    $(document).on("keyup paste keydown", ".enterQty", function() {
        let enterQty = parseFloat($(this).val()); // Convert input to float
        var rdcodeSt = $(this).data("rdcode");
        var maxqty = parseFloat($(this).data("maxval")); // Ensure maxval is also a float
        let rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0];
        let rdBatch = rdatrr[1];

        console.log(enterQty);

        if (!isNaN(enterQty) && enterQty <= maxqty) { // Ensure enterQty is a valid number
            if (enterQty > 0) {
                console.log("01");
                totalquentity(rdcodeSt);
                $('.batchCheckbox' + rdBatch).prop('checked', true);
            } else {
                if (enterQty > maxqty) {
                    $(this).val('');
                    console.log("02");
                    totalquentity(rdcodeSt);
                    $('.batchCheckbox' + rdBatch).prop('checked', false);
                }
            }
        } else {
            $(this).val('');
            console.log("03");
            totalquentity(rdcodeSt);
        }
    });


    $(document).on("keyup paste keydown", ".enterQtyManual", function() {
        let enterQty = $(this).val();
        var rdcodeSt = $(this).data("rdcode");
        rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0]; // Change the variable name to rdcode
        let rdBatch = rdatrr[1];

        console.log(enterQty);
        if (enterQty > 0) {
            console.log("01");
            totalquentity(rdcodeSt);
            $(".booktohicl_" + rdcode).html('<option value="+" selected>+</option><option value="-" disabled>-</option>');
        } else {
            $(this).val('');
            console.log("02");
            totalquentity(rdcodeSt);
            $(".booktohicl_" + rdcode).html('<option value="+" >+</option><option value="-" selected>-</option>');
        }
    });

    function totalquentitydiscut(rdcode) {

        $(".qty" + rdcode).each(function() {
            $(this).val('');
        });
        $("#itemSelectTotalQty_" + rdcode).html(inputQuantity(0));
        $("#itemQty_" + rdcode).val(0);
        $("#destination_itemQty_" + rdcode).val(0);
        $('.batchCbox').prop('checked', false);
    }

    function totalquentity(rdcodeSt) {
        let rdatrr = [];
        rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0]; // Change the variable name to rdcode
        let rdBatch = rdatrr[1];
        var sum = 0;

        $(".qty" + rdcode).each(function() {
            // Parse the value as a number and add it to the sum
            var value = parseFloat($(this).val()) || 0;
            sum += value;
        });

        console.log("Sum: " + sum);

        $("#itemSelectTotalQty_" + rdcode).html(inputQuantity(sum));
        $("#itemQty_" + rdcode).val(inputQuantity(sum));
        $("#destination_itemQty_" + rdcode).val(inputQuantity(sum));
        console.log('first => ' + rdcode);
        updateQty(rdcode);
    }
    // ***********************************************


    // invoice date *****************************************
    $("#creationDate").on("change", function(e) {
        //    console.log('firstDate');

        var creationDate = $(this).val();
        var rowData = {};
        let flag = 0;
        var spanItemsdestination = $("#spanItemsdestination").val();
        var spanItemsdestinationprod = $("#spanItemsdestinationprod").val();
        $(".itemRow").each(function() {
            flag++;
            let rowId = $(this).attr("id").split("_")[2];
            let itemId = $(this).attr("id").split("_")[1];
            rowData[rowId] = itemId;

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-stock-list.php`,
                data: {
                    act: "itemStock",
                    creationDate: creationDate,
                    itemId: itemId,
                    randCode: rowId,
                    mat: spanItemsdestination,
                    prod: spanItemsdestinationprod
                },
                beforeSend: function() {
                    // $(".tableDataBody").html(`<option value="">Loading...</option>`);
                },
                success: function(response) {
                    // $(`.customitemreleaseDiv${rowId}`).hide();
                    $(`.customitemreleaseDiv${rowId}`).html(response);
                }
            });

        });
        console.log('rowData');
        console.log(rowData);

        StringRowData = JSON.stringify(rowData);
        if (flag > 0) {
            Swal.fire({
                icon: `warning`,
                title: `Note`,
                text: `Available stock has been recalculated`,
                // showCancelButton: true,
                // confirmButtonColor: '#3085d6',
                // cancelButtonColor: '#d33',
                // confirmButtonText: 'Confirm'
            });


            $.ajax({
                type: "POST",
                url: `ajaxs/transfer/ajax-items-stock-check.php`,
                data: {
                    act: "itemStockCheck",
                    creationDate: creationDate,
                    rowData: StringRowData
                },
                beforeSend: function() {
                    $(".tableDataBody").html(`<option value="">Loading...</option>`);
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    let itemData = data.data;
                    console.log(data);
                    if (data.status === "success") {
                        for (let key in itemData) {
                            if (itemData.hasOwnProperty(key)) {

                                $(`#itemQty_${key}`).val(0);
                                $(`#checkQty_${key}`).val(itemData[key]);
                                $(`#checkQtySpan_${key}`).html(inputQuantity(itemData[key]));
                                // $(`#fifo_${key}`).prop('checked', true);
                                // $(`#itemSellType_${key}`).html('FIFO');
                                $(`.enterQty`).val('');
                            }
                        }
                    }
                }
            });
        }
    });

    $(document).ready(function() {
        loadItems();
        // **************************************
        function loadItems() {
            // alert();
            let value = $('#goodsType').val();
            let searchUrl = window.location.search;

            goodsType = (value != null && value != undefined) ? value : (searchUrl === "?create_service_invoice" ? 'service' : 'material');

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-goods-type.php`,
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loading...</option>`);
                },
                data: {
                    act: "goodsType",
                    goodsType: goodsType
                },
                success: function(response) {
                    $("#itemsDropDown").html(response);
                }
            });
        };

        // get item details by id
        $("#itemsDropDown").on("change", function() {

            let itemId = $(this).val();

            var creationDate = $("#creationDate").val();
            var spanItemsdestination = $("#spanItemsdestination").val();
            var spanItemsdestinationprod = $("#spanItemsdestinationprod").val();
            var movemenrtypesDropdown = $("#movemenrtypesDropdown").val();

            console.log(creationDate);
            if (itemId > 0) {
                // Disable submit button before request
                $("#directInvoiceCreationBtn").prop("disabled", true);

                $.ajax({
                    type: "GET",
                    url: `ajaxs/transfer/ajax-items-list-direct.php`,
                    data: {
                        act: "listItem",
                        creationDate: creationDate,
                        itemId,
                        mat: spanItemsdestination,
                        prod: spanItemsdestinationprod,
                        movemenrtypesDropdown: movemenrtypesDropdown
                    },
                    beforeSend: function() {
                        $("#spanItemsTable").html(`Loading...`);
                    },
                    success: function(response) {
                        $("#spanItemsTable").html(``);
                        $("#itemsTable").append(response);

                        // ✅ Enable button only if content is returned
                        if ($.trim(response) !== "") {
                            $("#directInvoiceCreationBtn").prop("disabled", false);
                        } else {
                            $("#directInvoiceCreationBtn").prop("disabled", true);
                        }
                    },
                    error: function() {
                        // ❌ Disable button if AJAX fails
                        $("#directInvoiceCreationBtn").prop("disabled", true);
                        $("#spanItemsTable").html(`<span class="text-danger">Failed to load item.</span>`);
                    }
                });
            }

        });

        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
        });


        $(document).on("change", ".destination_item_class", function() {

            var randcode = ($(this).attr("id")).split("_")[2];
            var itemId = $(this).val();
            // itemId=Number(itemId);
            var uom = $(this).find(':selected').data("uom");
            var uomId = $(this).find(':selected').data("uomid");
            var mwp = parseFloat($(this).find(':selected').data("mwp") || 0);
            let movtype = $("#movemenrtypesDropdown").val();

            if (itemId != 0 || itemId == null || itemId == undefined) {
                if (mwp > 0) {
                    console.log("Valid MWP:", mwp);
                } else {
                    if (movtype == "material_to_material") {
                        Swal.fire({
                            icon: "warning",
                            title: "Destination item's MWP is zero",
                            text: "System will use the source item's MWP instead.",
                            showConfirmButton: true,
                            confirmButtonText: "Okay"
                        });
                    }
                }
            }

            if ((itemId != 0 || itemId == null || itemId == undefined) && (movtype == "material_to_material")) {
                $.ajax({
                    type: "GET",
                    url: "ajaxs/ajax-item-failed-acc-checking.php",
                    data: {
                        act: 'checkItem',
                        itemId
                    },
                    success: function(response) {
                        try {
                            let res = JSON.parse(response);
                            if (res.status != 'success') {
                                Swal.fire({
                                    icon: res.status,
                                    title: "Item Status",
                                    text: res.message,
                                    showConfirmButton: true,
                                    confirmButtonText: "Okay"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let randcode2 = Number(randcode.toString().slice(0, 3));
                                        console.log(randcode2);

                                        let element = $('#delItemRowBtn_' + randcode2 + '_' + randcode);
                                        if (element.length) {
                                            element.remove();
                                        } else {
                                            console.log('Element not found with selector: #delItemRowBtn_' + randcode2 + '_' + randcode);
                                        }
                                    }
                                });
                                let randcode2 = Number(randcode.toString().slice(0, 3));
                                console.log(randcode2);

                                let element = $('#delItemRowBtn_' + randcode2 + '_' + randcode);
                                if (element.length) {
                                    element.remove();
                                } else {
                                    console.log('Element not found with selector: #delItemRowBtn_' + randcode2 + '_' + randcode);
                                }
                            }



                        } catch (e) {
                            console.error(e)
                        }
                    }
                });
            }


            $(`#destination_uom_${randcode}`).html(uom);
            $(`#destination_uom_hidden_${randcode}`).val(uomId);

            let disableButton = false;



            $(".destination_item_class").each(function() {
                let destItem = $(this).val();
                let mItm = $(this).data('mainitem');
                let mainItem = atob(mItm);

                if (destItem === mainItem) {
                    Swal.fire({
                        icon: "error",
                        title: "Cannot transfer the same item to the same item",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    disableButton = true;
                }
            });

            $('#directInvoiceCreationBtn').prop('disabled', disableButton);

        });

    }); // document ready end here

    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });

    $('#itemsDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });

    $('.select2').select2();
</script>

<script>
    let allowSubmitAfterModal = false;
    let allowSubmitAfterModal2 = false;


    $(document).on("click", "#directInvoiceCreationBtn", function(event) {
        let movtype = $("#movemenrtypesDropdown").val();

        // ✅ Case 1: NOT material_to_material — simple confirm & submit
        if (movtype !== "material_to_material") {
            if (!confirm("Are you sure you want to submit?")) {
                event.preventDefault();
            }
            return; // Skip rest
        }

        // ✅ Case 2: material_to_material — with validation + modal logic
        if (allowSubmitAfterModal) {
            allowSubmitAfterModal = false;
            return true; // Allow form submission after modal confirm
        }
        if (allowSubmitAfterModal2) {
            if (!confirm("Are you sure you want to submit?")) {
                event.preventDefault();
            }
            return; // Allow form submission after modal confirm
        }


        event.preventDefault(); // Always block submit initially
        let isValid = true;
        let showModal = false;

        // ✅ Qty Validation
        $(".itemQty").each(function() {
            $(this).next(".error-msg").remove();
            let value = $(this).val().trim();
            if (value === "" || isNaN(value) || parseFloat(value) <= 0) {
                isValid = false;
                let id = $(this).attr("id");
                let row = id.split("_")[1];
                $(`#qtyMsg_${row}`).show();
            }
        });

        if (!isValid) return;

        // ✅ Check UOM match
        $("#uom_table tbody").empty();

        $(".destination_itemQty").each(function() {
            let id = $(this).attr("id");
            let row = id.split("_")[2];

            let uomValue = $(`#source_uom_${row}`).val();
            let destinationUomValue = $(`#destination_uom_hidden_${row}`).val();
            let itemCode = $(`input[name="listItem[${row}][itemCode]"]`).val();
            let itemname = $(`input[name="listItem[${row}][itemName]"]`).val();
            let source_qty = $(`input[name="listItem[${row}][qty]"]`).val();
            let destination_qty = $(`input[name="listItem[${row}][destination_qty]"]`).val();
            let itemUom = $(`#destination_uom_${row}`).text();

            if (uomValue === destinationUomValue) {
                showModal = true;
                let newRow = `
                <tr>
                    <td>${itemCode}</td>
                    <td>${itemname}</td>
                    <td>${itemUom}</td>
                    <td>${source_qty}</td>
                    <td>${destination_qty}</td>
                </tr>`;
                $("#uom_table tbody").append(newRow);
            }
        });

        // ✅ Case 2a: UOM matches → show modal
        if (showModal) {
            $("#finalSubmitModal").modal("show");
            return;
        } else {

            allowSubmitAfterModal2 = true;
            $("#directInvoiceCreationBtn").trigger("click");
        }


    });

    // ✅ Final confirm after modal
    $(document).on("click", "#finalsubmit", function() {
        $("#finalSubmitModal").modal("hide");
        allowSubmitAfterModal = true;
        setTimeout(() => {
            $("#directInvoiceCreationBtn").trigger("click");
        }, 300);
    });




    $(document).on("input", ".itemQty", function() {
        let id = $(this).attr("id");
        let row = id.split("_")[1];
        $(`#qtyMsg_${row}`).hide();
    });

    function updateQty(row) {
        $(`#qtyMsg_${row}`).hide();
    }
</script>