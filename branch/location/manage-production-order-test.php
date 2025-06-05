<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-production-order-controller.php");
require_once("../../app/v1/functions/branch/func-stock-controller.php");


require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
echo "<br>bom.controller-test: ".require_once("bom/controller/bom.controller.test.php");
echo "<br>mrp.controller: ".require_once("bom/controller/mrp.controller.test.php");
echo "<br>consumption.controller: ".require_once("bom/controller/consumption.controller.test.php");

$productionOrderController = new ProductionOrderController();
$goodsBomController = new GoodsBomController();
$accountingControllerObj = new Accounting();

if (isset($_POST['addNewProduction'])) {
    // console($_POST);
    $productionOrder = $productionOrderController->createProduction($_POST);
    swalAlert($productionOrder["status"], ucfirst($productionOrder["status"]), $productionOrder["message"], BASE_URL . "branch/location/manage-production-order.php");
}

if (isset($_GET["run-mrp"])) {
    require_once("components/production/production-order-mrp-preview.php");
} elseif (isset($_GET["consumption-preview"])) {
    require_once("components/production/production-order-barcode-and-consumption.php");
} elseif (isset($_GET["create"])) {
?>
    <style>
        .card.pr-creation-card {
            height: auto;
        }

        .production-order-info-body {
            height: 160px !important;
        }
    </style>
    <div class="content-wrapper">
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="itemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="itemModalContent modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="itemModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Production Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Production Order</a></li>

                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="addNewProductionForm">
                    <input type="hidden" name="addNewProduction">

                    <div class="row">


                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card pr-creation-card so-creation-card">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-pen"></i>
                                                <h4>Info</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body others-info production-order-info-body">
                                    <div class="row others-info-form-view">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row others-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Expected Date<span class="text-danger">*</span></label>
                                                        <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $today ?>" />
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Reference Number</label>
                                                        <input id="refNo" type="text" name="refNo" class="form-control" />
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                                    <div class="form-input">
                                                        <!-- <label for="">Note</label> -->
                                                        <textarea name="description" class="form-control" placeholder="Note"><?= $row['description'] ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card item-select-table" style="overflow-x: auto;">
                                <table class="table table-sales-order">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Item Code</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <tr>
                                            <td><select name="item_id" class="form-control itemDrop" id="itemDrop">
                                                    <option>Select Item</option>
                                                    <?php
                                                    $item = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id`=$company_id AND `goodsType`= 3 ", true);
                                                    foreach ($item['data'] as $data) {
                                                    ?>
                                                        <option value="<?= $data['itemId'] ?>"><?php echo $data['itemName'] . "[" . $data['itemCode'] . ']';  ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <td id="itemCode"></td>
                                            <input type="hidden" name="itemCode" id="itemCodeHidden">
                                            <td id="itemDesc"></td>
                                            <td><input type="number" name="item_qty" class="form-control" min="1" step="0.01"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="addNewProductionFormSubmitBtn" id="prbtn" class="btn btn-xs btn-primary items-search-btn float-right">Submit</button>
            </div>
            </form>
    </div>
    </section>
    </div>
<?php
} else {
    require_once("components/production/production-order-list-test.php");
}
require_once("../common/footer.php");
?>
<script>
    $('#itemDrop')
        .select2()
        .on('select2:open', () => {});

    $("#itemDrop").on('change', function() {
        var itemId = $(this).val();
        $.ajax({
            type: "GET",
            url: `ajaxs/production/ajax-items.php`,
            data: {
                itemId
            },
            beforeSend: function() {

            },
            success: function(response) {
                var obj = jQuery.parseJSON(response);
                $("#itemCode").html(obj['code']);
                $("#itemCodeHidden").val(obj['code']);
                $("#itemDesc").html(obj['desc']);
            }
        });
    });
</script>