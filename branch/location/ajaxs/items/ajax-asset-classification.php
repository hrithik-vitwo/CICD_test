<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController(); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $val = $_GET['val'];

    if($_GET['act'] == "key"){
        //echo 1;
        $key_sql = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `depreciation_id`= '" . $val . "'");
      //  console($key_sql);
        echo $key = $key_sql['data']['desp_key'];

    }
   
else{
    
    //   echo 1;
   
    $parent = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `depreciation_id`= '" . $val . "'", true);

    $parent_code = $parent['data'][0]['desp_key'];

    $child = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `parent_code`= '" . $parent_code . "'", true);

    if ($child['numRows'] > 0) {
        $rand=rand(11,99);

?>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-input">
                <select name="asset_classification[]" data-classattr="asset_classification_new_<?= $rand;?>" class="form-control asset_classificationDropDown">
                    <option value="">Select Asset Classification</option>
                    <?php

                    foreach ($child['data'] as $data) {
                    ?>
                        <option value="<?= $data['depreciation_id'] ?>"><?= $data['asset_class'] ?></option>
                    <?php
                    }
                    ?>

                </select>
            </div>
        </div>
        <span class="asset_classification_new_<?= $rand;?>" style="display:none; display: inline-flex;  ">

        </span>

<?php

    } else {
    }
}
} else {
    echo "Something wrong, try again!";
}
