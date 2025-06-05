<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
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

$goodsController = new GoodsController();

if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    
    // console($_POST);
    $addNewObj = $goodsController->transfer_stock($_POST);

    // console($addNewObj);
    // exit;
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
                                                <option value="">Select</option>
                                                <option value="storage_location" selected>Storage Location to Storage Location</option>

                                            </select>

                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-12 cost-center-col">
                                            <div class="sl">

                                                <label for="">Destination Storage Location</label>
                                                <select name="destinationStorageLocation" class="select2 form-control " required>
                                                    <option value="">Select Storage Location</option>
                                                    <?php
                                                    $qrysrui= queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
                                                    $sldattaqe=$qrysrui['data'];
                                                    // console($sldattaqe);
                                                    foreach($sldattaqe as $datasllll){
                                                    ?>
                                                    <option value="<?= $datasllll['storage_location_id'].'|'.$datasllll['storageLocationTypeSlug'];?>"><?php echo $datasllll['warehouse_code'].' >> '.$datasllll['storage_location_code'].' >> '.$datasllll['storage_location_name']; ?></option>
                                                    <?php }?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <label for="date">Posting Date</label>
                                            <input type="date" name="creationDate" id="creationDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" value="<?= $min ?>" required>
                                        </div>




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

                            <div class="card">
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
                                    </table>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <button type="submit" name="addNewInvoiceFormSubmitBtn" onclick="return confirm('Are you sure to submitted?')" id="directInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
                    </div>
                </div>
        </form>
    </section>
</div>

<?php require_once("../common/footer.php"); ?>

<script>
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

    $(document).on("keyup paste keydown", ".enterQty", function() {
        let enterQty = $(this).val();
        var rdcodeSt = $(this).data("rdcode");
        var maxqty = $(this).data("maxval");
        let rdatrr = [];
        rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0]; // Change the variable name to rdcode
        let rdBatch = rdatrr[1];

        console.log(enterQty);
        if (enterQty <= maxqty) {
            if (enterQty > 0) {
                console.log("01");
                totalquentity(rdcodeSt);
                $('.batchCheckbox' + rdBatch).prop('checked', true);
            } else {
                $(this).val('');
                console.log("02");
                totalquentity(rdcodeSt);
                $('.batchCheckbox' + rdBatch).prop('checked', false);
            }
        } else {
            $(this).val('');
            console.log("03");
            totalquentity(rdcodeSt);
        }
    });

    function totalquentitydiscut(rdcode) {

        $(".qty" + rdcode).each(function() {
            $(this).val('');
        });
        $("#itemSelectTotalQty_" + rdcode).html(0);
        $("#itemQty_" + rdcode).val(0);
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

        // console.log("Sum: " + sum);

        $("#itemSelectTotalQty_" + rdcode).html(sum);
        $("#itemQty_" + rdcode).val(sum);
        console.log('first => ' + rdcode);
    }
    // ***********************************************

    
        // invoice date *****************************************
        $("#creationDate").on("change", function(e) {
            //    console.log('firstDate');

            var creationDate  = $(this).val();
            var rowData = {};
            let flag = 0;
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
                        creationDate : creationDate,
                        itemId: itemId,
                        randCode: rowId
                    },
                    beforeSend: function() {
                        // $(".tableDataBody").html(`<option value="">Loding...</option>`);
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
                        $(".tableDataBody").html(`<option value="">Loding...</option>`);
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
                                    $(`#checkQtySpan_${key}`).html(itemData[key]);
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
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
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
            console.log(creationDate);
            if (itemId > 0) {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/transfer/ajax-items-list-direct.php`,
                    data: {
                        act: "listItem",
                        creationDate: creationDate,
                        itemId
                    },
                    beforeSend: function() {
                        $(`#spanItemsTable`).html(`Loding...`);
                    },
                    success: function(response) {
                        $(`#spanItemsTable`).html(``);
                        $("#itemsTable").append(response);

                    }
                });
            }
        });

        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
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
</script>

<script>
    $(document).on("click", ".add_data", function() {
        var data = this.value;
        $("#createdatamultiform").val(data);
        // confirm('Are you sure to Submit?')
        $("#add_frm").submit();
    });
</script>