<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../../controller/gstr1-json-repositary-controller.php");
$queryParams = json_decode(base64_decode($_GET['action']));
// check auth
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
// console($authObj['status']);
// $authObj['status'] = "warning";
if ($authObj['status'] != 'success') {
    // if auth failed then return connect view
    require_once("./components/auth-connect.php");
} else { 
    // console($queryParams);
    $dbObj = new Database();
    $lastActivityObj=$dbObj->queryGet("SELECT * FROM `erp_compliance_gstr1` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `gstr1_return_period`='$queryParams->period'");
    $lastReturnFileStatus = $lastActivityObj["data"]["gstr1_return_file_status"] ?? 0;

    switch ($lastReturnFileStatus) {
        case 0:
        case 1:
        case 2:
            require_once("./components/gstr1-save-file.php");   
            break;
        case 3: //saved
            require_once("./components/gstr1-reset-procced.php");
            break;
        case 4: //reseted
            require_once("./components/gstr1-save-file.php");   
            break;
        case 5: //processed
            require_once("./components/gstr1-evc-and-file.php");
            break;
        case 6: // otp generated
            require_once("./components/gstr1-evc-and-file.php");
            break;    
        case 7: // verified and filed
            redirect(BASE_URL."branch/compliance/gstr1-concised-view.php");
            break;      
        default:
            redirect(BASE_URL."branch/compliance/gstr1-concised-view.php");
            break;
    }
    ?>
<?php } ?>



