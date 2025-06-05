<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //echo 1;
    $group_id = $_GET['val'];
    $get_child = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `groupParentId`= $group_id", true);
    // console($get_child);
    if ($get_child['numRows'] > 0) {
        //echo 1;
        $rand = rand(11, 99);
?>

        <div class="col-lg-6 col-md-6 col-sm-6 pr-0">
            <div class="form-input">
                <select name="goodsGroup[]" data-classattr="goodGroupDropDown_new_<?= $rand; ?>" class="form-control goodGroupDropDown" id="goodGroupDropDown">
                    <option value="">Select Group</option>
                    <?php

                    foreach ($get_child['data'] as $data) {
                    ?>
                        <option value="<?= $data['goodGroupId'] ?>"><?= $data['goodGroupName'] ?></option>
                    <?php
                    }
                    ?>

                </select>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 goodGroupDropDown_new_<?= $rand; ?>" style="display: block;">

        </div>

<?php
    } else {
    }
}

?>