<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");

$goodsBomController = new GoodsBomController();
$goodsController = new GoodsController();

function getRmSfgItems()
{
    global $location_id;
    $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                items.parentGlId,
                itemTypes.type,
                itemUom.uomName,
                COALESCE(summary.movingWeightedPrice,0.00) AS movingWeightedPrice,
                COALESCE(itemBom.cogm,0.00) AS itemBomPrice
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN `erp_inventory_items` AS items
            ON
                summary.`itemId` = items.`itemId`
            INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes
            ON
                items.`goodsType` = itemTypes.`goodTypeId`
            LEFT JOIN `erp_inventory_mstr_uom` AS itemUom
            ON
                items.`baseUnitMeasure` = itemUom.`uomId`
            LEFT JOIN `erp_bom` AS itemBom
            ON
                items.itemId = itemBom.itemId AND summary.`location_id` = itemBom.`locationId`
            WHERE
                summary.`location_id` = ' . $location_id . ' AND(
                    itemTypes.`type` = "RM" OR itemTypes.`type` = "SFG"
                )';

    return queryGet($sql, true);
}

function getGoodAndServiceItems($itemTypes = ["RM", "SFG"])
{
    global $location_id;
    $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                items.parentGlId,
                itemTypes.type,
                itemUom.uomName,
                COALESCE(summary.movingWeightedPrice,0.00) AS movingWeightedPrice,
                COALESCE(itemBom.cogm,0.00) AS itemBomPrice,
                summary.bomStatus
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN `erp_inventory_items` AS items
            ON
                summary.`itemId` = items.`itemId`
            INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes
            ON
                items.`goodsType` = itemTypes.`goodTypeId`
            LEFT JOIN `erp_inventory_mstr_uom` AS itemUom
            ON
                items.`baseUnitMeasure` = itemUom.`uomId`
            LEFT JOIN `erp_bom` AS itemBom
            ON
                items.itemId = itemBom.itemId AND summary.`location_id` = itemBom.`locationId`
            WHERE
                summary.`location_id` = ' . $location_id . ' AND itemTypes.`type` IN ("' . implode('","', $itemTypes) . '")';

    return queryGet($sql, true);
}


function getGoodActivities()
{
    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `CostCenter_id`,
                `CostCenter_code`,
                `CostCenter_desc`,
                `labour_hour_rate`,
                `machine_hour_rate`,
                `gl_code`,
                `parent_id`,
                `type`
            FROM
                `erp_cost_center`
            WHERE
                `CostCenter_status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `CostCenter_id`
            DESC';

    return queryGet($sql, true);
}

function getWcList()
{


    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `work_center_id`,
                `work_center_code`,
                `work_center_name`
            FROM
                `erp_work_center`
            WHERE
                `status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `work_center_id`
            DESC';

    return queryGet($sql, true);
}

$coaObj = getAllChartOfAccounts_list_by_p($company_id, 4);

// if (isset($_POST["addCOGSFormSubmitBtn"])) {
//     // console($_POST);
//     $createCogsObj = $goodsBomController->createBomCOGS($_POST);
//     swalToast($createCogsObj["status"], $createCogsObj["message"]);
// }

// if (isset($_POST["releaseBom"])) {
//     $bomId = base64_decode($_POST["releaseBom"]);
//     $updateCurrentBomItemPriceObj = $goodsBomController->updateCurrentBomItemPrice($bomId);
//     // console($updateCurrentBomItemPriceObj);
//     swalToast($updateCurrentBomItemPriceObj["status"], $updateCurrentBomItemPriceObj["message"]);
// }


?>

<style>
    .bom-modal .modal-dialog {
        max-width: 100%;
        width: 50%;
    }

    .bom-modal .modal-header {
        height: auto;
    }

    .bom-modal .modal-body {
        width: 100%;
        top: -30px;
    }

    .bom-modal .modal-body .card .card-body {
        padding: 15px 0 0px;
    }

    .bom-modal .modal-body .card .card-body table {
        margin-bottom: 20px;
    }

    .card.p-0.boq-form-card.bg-transparent.boq-section .card-body {
        overflow: auto;
        padding-left: 0;
        padding-right: 0;
    }

    .is-bom .card.p-0.boq-form-card.bg-transparent.boq-section .card-body .select2-container,
    .is-bom .card.p-0.boq-form-card.bg-transparent.boq-section .card-body select {
        width: 100% !important;
        max-width: 257px;
    }

    .is-bom .acc-summary {
        max-width: 500px;
        margin-left: auto;
    }

    .new-over-modal .form-input {
        margin: 7px 0;
    }

    .new-over-modal .select2-container {
        width: 100% !important;
    }
</style>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper is-bom">
    <section class="content">
        <div class="container-fluid">
            <!-- Create Bom -->
            <?php
            if (isset($_GET["create"]) && $_GET["create"] != "") {
                require_once("create.php");
            } elseif (isset($_GET["editBom"]) && $_GET["editBom"] != "") {
                require_once("editBom.php");
            } elseif (isset($_GET["view"]) && $_GET["view"] != "") {
                require_once("view.php");
            } elseif (isset($_GET["copy"]) && $_GET["copy"] != "") {
                require_once("copy.php");
            } else {

                $url = BRANCH_URL . 'location/bom.php';
                ?>
                    <script>
                        window.location.href = "<?php echo $url; ?>";
                    </script>
                <?php
            }
            ?>
            <!-- end Create Bom -->
        </div>
    </section>
</div>
<?php
require_once("../../common/footer.php");
?>