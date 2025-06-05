<?php
require_once("../../app/v1/config.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
global $dbCon;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $countyId = $_POST['county_id'];


    $query_sql = "SELECT * FROM `erp_gst_state_code` WHERE country_id = $countyId";
    $query = mysqli_query($dbCon, $query_sql);
    if ($countyId == 103 || $countyId == 14) {
        $options = ' <select id="state" name="state" class="form-control form-control-border borderColor">
                    <option value="">Select State</option>'; // Default option
        while ($row = mysqli_fetch_assoc($query)) {
            $options .= "<option value='{$row['gstStateName']}'>{$row['gstStateName']}</option>";
        }
        $options.='</select>';
    }else{
        $options ='<input type="text" name="state" class="m-input state_input" id="state_input">';
    }


    echo $options;
}
