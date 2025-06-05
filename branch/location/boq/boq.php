<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("controller/boq.controller.php");
$goodsController = new GoodsController();
$boqControllerObj = new BoqController();

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
                summary.`location_id` = ' . $location_id . ' AND itemTypes.`type` IN ("'.implode('","', $itemTypes).'")';

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
$coaObj = getAllChartOfAccounts_list_by_p($company_id, 4);
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid  mt-4">
            <!-- Create Bom -->
            <?php
            if (isset($_GET["create"]) && $_GET["create"] != "") {
                require_once("create.php");
            } elseif (isset($_GET["editBoq"]) && $_GET["editBoq"] != "") {
                require_once("list.php");
            } elseif (isset($_GET["view"]) && $_GET["view"] != "") {
                require_once("view.php");
            } else {
                require_once("list.php");
            }
            ?>
        </div>
    </section>
</div>
<?php
require_once("../../common/footer.php");
?>