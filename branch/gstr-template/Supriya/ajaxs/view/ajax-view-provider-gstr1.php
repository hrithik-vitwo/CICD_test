<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-compliance-controller.php");

// check auth
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

if ($authObj['status'] == 'success') {
    echo "success";
} else { ?>
    <!-- if auth failed then return connect view -->

    <div id="loginFirstDiv">
        <p>You have to login first!</p>
        <button id="nextStage">Next Stage</button>
    </div>

    <div id="connectAuthDiv">
    </div>
<?php } ?>



