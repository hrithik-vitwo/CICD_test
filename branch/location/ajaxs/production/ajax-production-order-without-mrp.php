    <?php
    require_once("../../../../app/v1/connection-branch-admin.php");

    $dbObj = new Database();
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $productionOrderId = base64_decode($_GET["prodId"]);

        $stausUpdateSql = $dbObj->queryUpdate('UPDATE `erp_production_order` SET `mrp_status`="Not Created", `status`="13", `updated_by`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `so_por_id`=' . $productionOrderId);

        if ($stausUpdateSql['status'] == 'success') {
            $res = [
                "status" => 'success',
                "message" => "Order released successfully",
                "sql" => $stausUpdateSql,
            ];
        } else {
            $res = [
                "status" => 'warning',
                "message" => "Order released failed !",
            ];
        }


        echo json_encode($res);
    }
