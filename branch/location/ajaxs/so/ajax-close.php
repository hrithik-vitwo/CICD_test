<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "closeSo") {
    $soId = $_GET['soId'];
    global $updated_by;
    $soDetails = queryGet("SELECT * FROM `erp_branch_sales_order` WHERE so_id=$soId")['data'];
    $customerId = $soDetails['customer_id'];
    $SONumber = $soDetails['so_number'];
    $soDate=$soDetails['so_date'];
    $postingTime=$soDetails['soPostingTime'];
    $deliveryDate=$soDetails['delivery_date'];
    $billingAddress=$soDetails['billingAddress'];
    $curr_rate=$soDetails['soPostingTime'];
    $creditPeriod=$soDetails['soPostingTime'];
    $shippingAddress=$soDetails['shippingAddress'];
    $curr_rate=$soDetails['conversion_rate'];
    $goodsType=$soDetails['goodsType'];
    $currencyName=$soDetails['currency_name'];
    $customerPO=$soDetails['customer_po_no'];
    $soStatus=$soDetails['soStatus'];
    $createdby=$soDetails['created_by'];
    $updatedby=$soDetails['updated_by'];
    $currentTime = date("Y-m-d H:i:s");
    $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=10 WHERE so_id='" . $soId . "'";
    $updateObj = queryUpdate($upd);
    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail['basicDetail']['trail_type'] = 'REJECT'; //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER;
        $auditTrail['basicDetail']['column_name'] = 'so_id'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $soId;  // primary key
        $auditTrail['basicDetail']['party_type'] = 'customer';
        $auditTrail['basicDetail']['party_id'] = $customerId;
        $auditTrail['basicDetail']['document_number'] = $SONumber;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = 'Seles Order Closed';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Updated';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Sales Order Detail']['So_number'] = $returnSoNo;
        $auditTrail['action_data']['Sales Order Detail']['So_date'] = formatDateWeb($soDate);
        $auditTrail['action_data']['Sales Order Detail']['SoPostingTime'] = formatDateWeb($postingTime);
        $auditTrail['action_data']['Sales Order Detail']['Delivery_date'] = formatDateWeb($deliveryDate);
        $auditTrail['action_data']['Sales Order Detail']['Billing Address'] = $billingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Shipping Address'] = $shippingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Credit_period'] = $creditPeriod;
        $auditTrail['action_data']['Sales Order Detail']['Conversion_rate'] = decimalValuePreview($curr_rate);
        $auditTrail['action_data']['Sales Order Detail']['Currency_name'] = $currencyName;
        $auditTrail['action_data']['Sales Order Detail']['GoodsType'] = $goodsType;
        $auditTrail['action_data']['Sales Order Detail']['Customer Order No'] = $customerPO;
        $auditTrail['action_data']['Sales Order Detail']['SoStatus'] = 'open';
        $auditTrail['action_data']['Sales Order Detail']['Created_by'] = getCreatedByUser($createdby);
        $auditTrail['action_data']['Sales Order Detail']['Updated_by'] = getCreatedByUser($updatedby);


        $auditTrail['action_data']['Closed Details']['Close By'] = getCreatedByUser($updated_by);
        $auditTrail['action_data']['Closed Details']['Close At'] = formatDateTime($currentTime);

        $auditTrailreturn = generateAuditTrail($auditTrail);

    echo json_encode($updateObj);
?>

<?php
} elseif ($_GET['act'] === "closeQuotation") {
    $quotationId = $_GET['quotationId'];
    $upd = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET approvalStatus=10 WHERE quotation_id='" . $quotationId . "'";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);
} elseif ($_GET['act'] === "approvalTab") {
    $soId = $_GET['soId'];
    $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=9 WHERE so_id='" . $soId . "'";
    if ($dbCon->query($upd)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Something wrong, try again!";
}
?>