<?php

require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-compliance-controller.php");


$authGstinPortalObj = new AuthGstinPortal();

if(isset($_POST["act"]) && $_POST["act"]=="sendOtp"){
    $sendOtpObj=$authGstinPortalObj->sendOtp();
    echo json_encode($sendOtpObj, true);

}elseif(isset($_POST["act"]) && $_POST["act"]=="verifyOtp"){
    $authOtp=$_POST["authOtp"] ?? "";
    $verifyOtpObj=$authGstinPortalObj->verifyOtp($authOtp);
    echo json_encode($verifyOtpObj, true);
}else{
    $authObj = $authGstinPortalObj->checkAuth();
    if($authObj["status"]!="success"){
        http_response_code(401);
        ?>
        <div id="complianceAuthDiv" class="row p-0 m-0">
        <?= $authObj["message"] ?>
        <button class="btn btn-primary">Send Otp</button>
        </div>
        <?php
    }else{
        http_response_code(200);
        ?>
        <div id="complianceAuthDiv" class="row p-0 m-0">
            <p class="text-success"><?= $authObj["message"]; ?></p>
        </div>
        <?php
    }
}
?>
</div>