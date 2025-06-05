<?php

require_once("../../../../../../app/v1/connection-branch-admin.php");


// if ($_SERVER['REQUEST_METHOD'] == "POST") {
?>

<h4 class="text-sm font-bold m-4">Connect Portal</h4>
    <div class="text">
        <p class="text-sm">OTP has been sent to the registered mobile number.</p>
        <p class="text-sm">Please enter the otp and processed</p>
        <div id="OtpInputs" class="otp-inputs d-flex gap-2 my-3">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
            <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
        </div>
        <a class="btn btn-primary waves-effect waves-light" href="./gst-1-pending-save-data.php">Proceed</a>
    </div>


<?php


// } else {
//     console("METHOD_ERROR");
// }

?>