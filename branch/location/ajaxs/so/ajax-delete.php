<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$dbObj = new Database();
$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "deleteSo") {
    $soNum = $_GET['soNum'];
    $upd = "UPDATE erp_branch_sales_order set  approvalStatus= 5  WHERE so_number='".$soNum."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);

}elseif($_GET['act']=== 'deleteSoall'){

    $soNum = $_GET['soNum'];
    $upd = "UPDATE erp_branch_sales_order set  approvalStatus= 5, status = 'deleted'  WHERE so_number='".$soNum."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);

}elseif($_GET['act']=== 'sodelivery'){    
    $soNum = $_GET['soNum'];
    $upd = "UPDATE `erp_branch_sales_order_delivery` set  status = 'deleted'  WHERE so_number='".$soNum."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);


}
elseif($_GET['act']=== 'soPgi'){    
    $soPgi = $_GET['soPgi'];
    $upd = "UPDATE `erp_branch_sales_order_delivery_pgi` set  status = 'deleted'  WHERE so_delivery_pgi_id='".$soPgi."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);


}

elseif($_GET['act']=== 'soProformaInv'){    
    $proformaInvNo = $_GET['proformaInvNo'];
    $upd = "UPDATE `erp_proforma_invoices` set  status = 'deleted'  WHERE invoice_no='".$proformaInvNo."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);


}

elseif($_GET['act']=== 'soQuotation'){    
    $quotationId = $_GET['quotationId'];
    $upd = "UPDATE `erp_branch_quotations` set  status = 'deleted'  WHERE quotation_id='".$quotationId."';";
    $updateObj = queryUpdate($upd);
    echo json_encode($updateObj);


}

elseif($_GET['act']=== 'payroll'){    
    
$payroll_main_id = $_GET['payrollMainId'];

$payrollMainTable= $dbObj->queryDelete("DELETE FROM `erp_payroll_main` WHERE payroll_main_id =".$payroll_main_id);
$payrollTable =$dbObj->queryDelete("DELETE FROM  `erp_payroll` WHERE payroll_main_id =".$payroll_main_id);

if($payrollMainTable['status']=='success' && $payrollTable['status']=='success'){
    $returnData = [
        "status" => "success",
        "message" => "Data deleted successfully",
        
    ];
}else{
    $returnData = [
        "status" => "failed",
        "message" => "Data deleted failed, try again later",
        
    ];
}
echo json_encode($returnData);


}