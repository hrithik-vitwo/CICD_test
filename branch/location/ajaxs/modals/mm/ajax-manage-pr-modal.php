<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-manage-pr.php");
require_once("../../../../../app/v1/functions/branch/func-branch-pr-controller.php");
$headerData = array('Content-Type: application/json');

$BranchPrObj = new BranchPr();
$tempObj=new TemplatePr();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET['act'] == 'modalData') {

    $pr_id = $_GET['pr_id'];
    $cond = "AND pr.purchaseRequestId ='" . $pr_id . "'";

    $sql_list = "SELECT pr.*,admin.fldAdminName,stat.label FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr LEFT JOIN tbl_branch_admin_details as admin on pr.created_by=admin.fldAdminKey   LEFT JOIN `erp_status_master` AS stat
                 ON pr.pr_status = stat.status_id WHERE 1 " . $cond . "  AND pr.company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . "  ORDER BY purchaseRequestId desc";

    $prPoExist = queryGet("SELECT EXISTS (
        SELECT 1 
        FROM erp_branch_purchase_order 
        WHERE pr_id = $pr_id
    ) AS exists_flag;")['data']['exists_flag'];

    $sqlMainQryObj = queryGet($sql_list);
    $sqldata = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];
    $created_by=getCreatedByUser($sqldata['created_by']);
    $created_at=formatDateORDateTime($sqldata['created_at']);
    $updated_by=getCreatedByUser($sqldata['updated_by']);
    $updated_at=formatDateORDateTime($sqldata['updated_at']);





    if ($num_list > 0) {
        $dynamic_data = [];
        // $itemDetails = $BranchPrObj->fetchBranchPrItems($sqldata['purchaseRequestId'])['data'];
        $itemDetailssql = "SELECT
                            prItem.*,
                            delv.*,
                            uom.uomName
                        FROM
                            `erp_branch_purchase_request_items`  as prItem
                        LEFT JOIN erp_inventory_mstr_uom as uom ON uom.uomId = prItem.uom
                        LEFT JOIN `erp_purchase_register_item_delivery_schedule` as delv
                        ON prItem.prItemId=delv.pr_item_id
                        WHERE
                        prItem.prId = ".$sqldata['purchaseRequestId']." AND prItem.company_id = '" . $company_id . "' AND prItem.branch_id = '" . $branch_id. "' AND prItem.location_id = '" . $location_id. "';";
       
        $itemDeialsobj=queryGet($itemDetailssql,true)['data'];

        $itemDetail=[];

        foreach($itemDeialsobj as $oneitem){

        if($oneitem['remaining_qty']== 0){
            $status='<p>PO Created</p>';
        }else{
            $itemSql=queryGet("SELECT * FROM `erp_rfq_items` WHERE deliverySceduleId=".$oneitem['pr_delivery_id']."	");
            $numRows=$itemSql['numRows'];
            if($numRows>0){
                $status='<p>RFQ</p>';
            }
        }

        $itemDetail[]=[
            "itemId"=>$oneitem['itemId'],
            "pr_delivery_id"=>$oneitem['pr_delivery_id'],
            "itemCode"=>$oneitem['itemCode'],
            "itemName"=>$oneitem['itemName'],
            "qty"=>$oneitem['qty'],
            "remaining_qty"=>$oneitem['remaining_qty'],
            "uomName"=>$oneitem['uomName'],
            "delivery_date"=>$oneitem['delivery_date'],
            "status"=>$status
        ];
    }
       
       $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $sqldata,
            "item_details" => $itemDetail,
            "created_by"=>$created_by,
            "created_at"=>$created_at,
            "updated_by"=>$updated_by,
            "updated_at"=>$updated_at,
            "prPoExist" => ($prPoExist == '0') ? false : true
        ];
    } else {

        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
}else if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $addBranchRfq = $BranchPrObj->addBranchAddtoRFQ(($_POST));
    //  console($addBranchRfq);
  //  exit();
    echo json_encode($addBranchRfq);
}else if($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'classicView'){
    $pr_id = $_GET['pr_id'];
    $tempObj->printManagePr($pr_id);
}
