<?php
require_once("../../../app/v1/connection-branch-admin.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $country = $_POST['countryId'];
    $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`  WHERE `country_id` = $country ", true);
   // console($state_sql);
    $state_data = $state_sql['data'];

    foreach ($state_data as $data){

        ?>

          <option value="<?= $data['gstStateName'] ?>" <?php if ($data['gstStateName'] == $gstDetails['pradr']['addr']['stcd']) {
                                                          // echo "selected";
                                                        } ?>><?= $data['gstStateName'] ?></option>

                                                        <?php
    }
}
      

?> 