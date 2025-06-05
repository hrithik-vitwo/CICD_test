<?php
function sendEmail($POST){
 
       global $dbCon;
       global $company_id;
       global $companyNameNav;

       if (!isset($_SESSION["logedCompanyAdminInfo"]["adminId"]) || !isset($_SESSION["logedCompanyAdminInfo"]["adminRole"])) {
             redirect(COMPANY_URL . "login.php");
        } else {
       $returnData = [];
       $isValidate = validate($POST, [
           "email" => "required"
       ]);

       if ($isValidate["status"] == "success") {

        // $otp = rand(110001, 999999);
        $user_id = $_SESSION["logedCompanyAdminInfo"]["adminId"];

        $created_by = $user_id."|location";

        // $ins_query = "INSERT INTO `erp_migration_otp` SET `otp`='".$otp."', `company_id`=$company_id, `created_by`=$created_by, `updated_by`=$created_by, `user_id`=$user_id ";
        // $data = queryInsert($ins_query);

        // $company_query = queryGet('SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminCompanyId`=' . $company_id .' ORDER BY `fldAdminKey` ASC LIMIT 1');

        // $company_email = $company_query["data"]["fldAdminEmail"];
        // $sub = "Migration OTP Mail";
        // $msg = "OTP: ".$otp;
        // SendMailByMySMTPmailTemplate($company_email,$sub,$msg);


        $sub = "Migration Mail";
        $payloads = base64_encode(base64_encode($company_id."|".$user_id));
        $msg = "Hi! User,<br> This below url is for ".$companyNameNav." migration <br>
        URL: https://migration.one.vitwo.ai/".$payloads." <br>
        <note>This is two factor authenticated URL</note>";
        SendMailByMySMTPmailTemplate($POST["email"],$sub,$msg);

        $returnData['status'] = "success";
        $returnData['message'] = "Mail Sent Successfuly";
        // $returnData['otp'] = $otp;
        // $returnData['url'] = $msg;
        
       }
       else
       {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
       }

       return $returnData;

    }

    }
?>