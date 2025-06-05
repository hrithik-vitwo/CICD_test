<?php
require_once("config.php");
require_once("tables.php");
require_once("lib/validator/autoload.php");
require_once("lib/phpSpreadsheet/autoload.php");
require_once("lib/vendor/autoload.php");
require_once("functions/common/func-common.php");
require_once("functions/branch/func-branch-administrator.php");
require_once("functions/branch/func-branch-admin.php");




  

if (isset($_SESSION["logedBranchAdminInfo"])) {
    $company_id = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] ?? '';
    $branch_id = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] ?? ''; 
    $location_id = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"] ?? '';
    $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];


    $quickcontact='Tel: 330212565845';
    
    
    define("COMP_STORAGE_DIR", BUCKET_DIR."uploads/$company_id");
    define("COMP_STORAGE_URL", BUCKET_URL."uploads/$company_id");
    
 

    if (isset($_SESSION["logedBranchAdminInfo"]["flAdminVariant"])) {
        $vrntsql = "SELECT flAdminVariant from tbl_branch_admin_details where fldAdminKey=".$_SESSION["logedBranchAdminInfo"]["adminId"]."";
        $vrntDat = queryGet($vrntsql)['data'];
        $admin_variant = $vrntDat["flAdminVariant"] ?? '';
    } else {
        $vrntsql = "SELECT flAdminVariant from tbl_branch_admin_details where fldAdminKey=".$_SESSION["visitBranchAdminInfo"]["adminId"]."";
        $vrntDat = queryGet($vrntsql)['data'];
        $admin_variant = $vrntDat["flAdminVariant"] ?? '';
    }

// echo $vrntsql;
//       print_r($vrntDat);
    if (!empty($company_id)) {
        $compsql = "Select company_name,company_code,decimal_quantity,decimal_value,opening_date,isPoEnabled,invoice_template_id,isWhatsappActive,isEmailActive,company_pan,company_const_of_business,company_currency FROM " . ERP_COMPANIES . " where company_id=$company_id";
        $compDat = queryGet($compsql)['data'];
        // console($compDat);
        $companyNameNav = $compDat['company_name'];
        $companyCodeNav = $compDat['company_code'];
        $companyPAN = $compDat['company_pan'];
        $companyCOB = $compDat['company_const_of_business'];
        $isPoEnabled = $compDat['isPoEnabled']; 
        $compOpeningDate = $compDat['opening_date']; 
        $isEmailActive = $compDat['isEmailActive']; 
        $isWhatsappActive = $compDat['isWhatsappActive']; 
        $company_currency = $compDat['company_currency']; 
        $invoice_template_id = $compDat['invoice_template_id'];        
        $decimalQuantity = $compDat['decimal_quantity']; 
        $decimalValue = $compDat['decimal_value']; 
    }
    if (!empty($branch_id)) {
        $branchsql = "Select branch_name,branch_code,state,branch_gstin from " . ERP_BRANCHES . " where branch_id=$branch_id";
        $branchResponce=queryGet($branchsql)['data'];
        $branchNameNav = $branchResponce['state'];
        $branch_gstin = $branchResponce['branch_gstin'];
    }
    if (!empty($location_id)) {
        $locatinSql = "Select othersLocation_code,othersLocation_name,othersLocation_city,companyFunctionalities from " . ERP_BRANCH_OTHERSLOCATION . " where othersLocation_id=$location_id";
        $locatinResponce=queryGet($locatinSql)['data'];
        $locationNameNav = $locatinResponce['othersLocation_name'];
        $companyFunctionalities = $locatinResponce['companyFunctionalities'];
        
    }

    if (isset($_SESSION["visitCompanyAdminInfo"]) && !isset($_SESSION["visitBranchAdminInfo"])) {
        $current_userName=$_SESSION["visitCompanyAdminInfo"]["adminName"];
        $created_by = $_SESSION["visitCompanyAdminInfo"]["adminId"] . '|' . $_SESSION["visitCompanyAdminInfo"]["adminType"];
        $updated_by = $_SESSION["visitCompanyAdminInfo"]["adminId"] . '|' . $_SESSION["visitCompanyAdminInfo"]["adminType"];
    } else if (!isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) {
        $current_userName=$_SESSION["visitBranchAdminInfo"]["adminName"];
        $created_by = $_SESSION["visitBranchAdminInfo"]["adminId"] . '|' . $_SESSION["visitBranchAdminInfo"]["adminType"];
        $updated_by = $_SESSION["visitBranchAdminInfo"]["adminId"] . '|' . $_SESSION["visitBranchAdminInfo"]["adminType"];
    } else if (isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) {
        $current_userName=$_SESSION["visitCompanyAdminInfo"]["adminName"];
        $created_by = $_SESSION["visitCompanyAdminInfo"]["adminId"] . '|' . $_SESSION["visitCompanyAdminInfo"]["adminType"];
        $updated_by = $_SESSION["visitCompanyAdminInfo"]["adminId"] . '|' . $_SESSION["visitCompanyAdminInfo"]["adminType"];
    } else {
        $current_userName=$_SESSION["logedBranchAdminInfo"]["adminName"];
        $created_by = $_SESSION["logedBranchAdminInfo"]["adminId"] . '|' . $_SESSION["logedBranchAdminInfo"]["adminType"];
        $updated_by = $_SESSION["logedBranchAdminInfo"]["adminId"] . '|' . $_SESSION["logedBranchAdminInfo"]["adminType"];
    }
    require_once("tables2.php");

    require_once(dirname(__DIR__, 2)."/core/autoload.php");
}
