<?php
    require_once("config.php");
    require_once("tables.php");
    require_once("lib/validator/autoload.php");
    require_once("functions/common/func-common.php"); 
    require_once("functions/company/func-company-administrator.php");
    require_once("functions/company/func-company-admin.php");

    
    if(isset($_SESSION["logedCompanyAdminInfo"])){
        $current_userName=$_SESSION["logedCompanyAdminInfo"]["adminName"];
        $company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]??'';
        $created_by=$_SESSION["logedCompanyAdminInfo"]["adminId"].'|'.$_SESSION["logedCompanyAdminInfo"]["adminType"];
        $updated_by=$_SESSION["logedCompanyAdminInfo"]["adminId"].'|'.$_SESSION["logedCompanyAdminInfo"]["adminType"];
        $companyCountry = $_SESSION['logedCompanyAdminInfo']['companyCountry']; 
           
        $quickcontact='Tel: 330212565845';
        
    
        define("COMP_STORAGE_DIR", BUCKET_DIR."uploads/$company_id");
        define("COMP_STORAGE_URL", BUCKET_URL."uploads/$company_id");
        
        require_once("tables2.php");
        if (!empty($company_id)) {
            $compsql = "Select company_name,company_code,opening_date,isPoEnabled,decimal_quantity,decimal_value,isWhatsappActive,isEmailActive,company_pan,company_const_of_business,company_currency,company_country from " . ERP_COMPANIES . " where company_id=$company_id";
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
            $decimalQuantity = $compDat['decimal_quantity']; 
            $decimalValue = $compDat['decimal_value']; 
            $companyCountry = $compDat['company_country'];
        }
    }
?>