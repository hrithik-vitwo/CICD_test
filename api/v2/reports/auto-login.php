<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/lib/jwt/autoload.php");

if (isset($_SESSION["logedBranchAdminInfo"]) && count($_SESSION["logedBranchAdminInfo"]) > 0) {
    $data = [
        "company_id" => intval($company_id),
        "branch_id" => intval($branch_id),
        "location_id" => intval($location_id),
        "created_by" => $created_by,
        "updated_by" => $updated_by,
        "authUserId" => intval($_SESSION["logedBranchAdminInfo"]["adminId"]),
        "authUserName" => $_SESSION["logedBranchAdminInfo"]["adminName"],
        "authUserEmail" => $_SESSION["logedBranchAdminInfo"]["adminEmail"],
        "authUserVariant" => intval($_SESSION["logedBranchAdminInfo"]["flAdminVariant"]),
        "authUserRole" => intval($_SESSION["logedBranchAdminInfo"]["adminRole"]),
        "authUserType" => $_SESSION["logedBranchAdminInfo"]["adminType"],
        "companyCurrency" => intval($company_currency),
        "compOpeningDate" => $compOpeningDate,
        "isPoEnabled" => intval($isPoEnabled),
        "companyName" => $companyNameNav,
        "companyCode" => $companyCodeNav,
        "companyPAN" => $companyPAN,
        "companyCOB" => $companyCOB,
        "branchName" => $branchResponce["state"],
        "branchCode" => $branchResponce["branch_code"],
        "branchGstin" => $branchResponce["branch_gstin"],
        "locationName" => $locatinResponce["othersLocation_name"],
        "locationCode" => $locatinResponce["othersLocation_code"],
        "locationCity" => $locatinResponce["othersLocation_city"],
        "decimalPlaces" => 2,
    ];

    $jwtObj = new JwtToken();
    $jwtToken = $jwtObj->createToken([
        "data" => $data
    ], (60 * 60 * 8)); // 8 hrs

    if(isset($_GET["url"]) && !empty($_GET["url"])){
        header("Location: ".$_GET["url"]."?token=".$jwtToken);
    }else{
        header("Location: https://reports.one.vitwo.ai?token=".$jwtToken);
    }
} else {
    header("Location: https://one.vitwo.ai/q1");
}
