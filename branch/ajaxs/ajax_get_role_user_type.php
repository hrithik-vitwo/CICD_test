<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-others-location.php");
$menufor = $_POST['menufor'];
$getAdministratorRoleDetails = getAdministratorRoleDetails($menufor);
?>
<?php
if ($getAdministratorRoleDetails['data']['fldRoleAccesses'] == 'Branch') { ?>
    <select name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" class="form-control" required>
        <option value="0">Branch Lavel</option>
    </select>
<?php } else { ?>
    <select name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" class="form-control" required>
        <option value="">---- Select One ----</option>
        <?php
        $listResult = getAllDataBranchLocationActive();
        if ($listResult["status"] == "success") {
            foreach ($listResult["data"] as $listRow) { ?>
                <option value="<?= $listRow["othersLocation_id"]; ?>"><?= $listRow["othersLocation_name"]; ?> [<?= $listRow["othersLocation_code"]; ?>]</option>
        <?php   }
        }
        ?>
    </select>
<?php } ?>