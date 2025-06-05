<?php

require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_GET['act'] === "oldterritoryname") {
	$teritaryId=$_GET['territoryid'];
	$oldQuery=queryGet("SELECT state_codes FROM `erp_mrp_territory` WHERE territory_id=$teritaryId;");
    $stateQuery = queryGet("SELECT gstStateCode,gstStateName FROM `erp_gst_state_code` WHERE `country_id` = $companyCountry", true);

	$oldQueryData=$oldQuery['data']['state_codes'];
	$oldArray=unserialize($oldQueryData);
// console($oldArray);
// exit();
    foreach ($stateQuery['data'] as $data) {
?>

        <label class="dropdown-option">
            <input type="checkbox" name="stateCode[]" value="<?= $data['gstStateCode'] ?>" <?php echo (in_array($data['gstStateCode'], $oldArray, TRUE)) ? 'checked' : ''; ?> >
            <?php echo $data['gstStateName'] . '-' . $data['gstStateCode']; ?>
        </label>

<?php }
}
