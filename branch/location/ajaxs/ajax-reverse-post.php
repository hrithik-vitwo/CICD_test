<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-reverse-posting.php");

$reversePostingObj = new ReversePosting();
if (isset($_POST['dep_slug']) && !empty($_POST['dep_keys'])) {

    if ($_POST['dep_slug'] == 'reverseDepreciation') {
        $return = $reversePostingObj->reverseDepreciation($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseDelivery') {
        $return = $reversePostingObj->reverseDelivery($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reversePGI') {
        $return = $reversePostingObj->reversePGI($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseInvoice') {
        $return = $reversePostingObj->reverseInvoice($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseGRN') {
        $return = $reversePostingObj->reverseGRN($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseGRNIV') {
        $return = $reversePostingObj->reverseGRNIV($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseJOURNAL') {
        $return = $reversePostingObj->reverseJOURNAL($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reversePayment') {
        $return = $reversePostingObj->reversePayment($_POST['dep_keys']);
        echo json_encode($return);
    } else if ($_POST['dep_slug'] == 'reverseCollection') {
        $return = $reversePostingObj->reverseCollection($_POST['dep_keys']);
        echo json_encode($return);
    }
    else if ($_POST['dep_slug'] == 'reverseCollectionFailedAccounting') {
        $return = $reversePostingObj->reverseCollectionFailedAccounting($_POST['dep_keys']);
        echo json_encode($return);
    }
    else if ($_POST['dep_slug'] == 'reverseProdDeclaration') {
        $return = $reversePostingObj->reverseProdDeclaration($_POST['dep_keys']);
        echo json_encode($return);
    }
    else if ($_POST['dep_slug'] == 'reverseDebitNote') {
        // echo $_POST['dep_keys'];
        $return = $reversePostingObj->reverseDebitNote($_POST['dep_keys']);
        echo json_encode($return);
    }
    else if ($_POST['dep_slug'] == 'reverseCreditNote') {
        // echo $_POST['dep_keys'];
        $return = $reversePostingObj->reverseCreditNote($_POST['dep_keys']);
        echo json_encode($return);
    }

} else {
    $return = [
        "status" => "warning",
        "message" => "Something went wrong!",
    ];
    echo json_encode($return);
}
