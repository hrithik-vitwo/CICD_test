<?php

require_once("../../../../../app/v1/connection-branch-admin.php");


if ($_SERVER['REQUEST_METHOD'] == "POST") {
?>

    <h4 class="text-sm font-bold m-4">Generate PVC</h4>
    <div class="generate-box text-center" id="generateBox">
        <img src="<?= BASE_URL ?>public/assets/img/VitNew 1.png" alt="">
        <div class="text">
            <p class="text-sm">If you want to generate EVC then click on </p>
            <button class="btn btn-primary border">Proceed to file</button>
        </div>
        <p class="text-sm font-bold my-3">Or</p>
        <div class="text">
            <p class="text-sm">If you want to generate EVC then click on </p>
            <button class="btn btn-primary reset-btn">Reset file</button>
        </div>
    </div>
<?php


} else {
    console("METHOD_ERROR");
}

?>