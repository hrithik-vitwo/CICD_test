<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');

// queryGet
if (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] != "") {
    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
    $keyWord = $_GET["keyWord"] ?? "";
    if($keyWord!=""){
        $sql = "SELECT * FROM `erp_cost_center` WHERE `company_id`=".$companyID." AND  (`CostCenter_code` LIKE '%".$keyWord."%' OR `CostCenter_desc` LIKE '%".$keyWord."%')";
        $activitiesObj = queryGet($sql, true);
        if($activitiesObj["status"] == "success"){
            foreach($activitiesObj["data"] as $oneActivity) {
                echo '<span class="dropdown-item btn dropdownGoodActivity" data-id="'.$oneActivity["CostCenter_id"].'" data-title="'.$oneActivity["CostCenter_desc"].'" data-costcentercode="'.$oneActivity["CostCenter_code"].'" data-lhr="'.$oneActivity["labour_hour_rate"].'" data-mhr="'.$oneActivity["machine_hour_rate"].'" >'.$oneActivity["CostCenter_code"].' - '.$oneActivity["CostCenter_desc"].'</span>';
            }
        }else{
            echo '<span class="dropdown-item btn dropdownGoodActivity" data-id="0">Not found...</span>';
        }
    }else{
        echo '<span class="dropdown-item btn dropdownGoodActivity" data-id="0">Enter keyword for search...</span>';
    }
} else {
    echo "Please do login first";
}
