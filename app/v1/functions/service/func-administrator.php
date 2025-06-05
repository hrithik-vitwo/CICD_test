<?php

function administratorAuth()
{
    // console($_SESSION);
    // echo SERVICE_URL;
    //exit();
    global $dbCon;
    $commonAccessPages = ["index.php"];
    if (!isset($_SESSION["logedAdminInfo"]["adminId"]) || !isset($_SESSION["logedAdminInfo"]["adminType"])) {
        echo 1;
        redirect(SERVICE_URL . "login.php");
    } else {
        echo 2;
        $adminType = $_SESSION["logedAdminInfo"]["adminType"]; 
        if ($adminType == 'Performer') {
            echo 3;
            $url = SERVICE_URL."index.php";
            redirect($url);
          
            
        }
       
    }
}


function getAdministratorMenuSubMenu()
{
    global $dbCon; $returnData = []; $accesses = "";
    $adminRole = $_SESSION["logedAdminInfo"]["adminRole"];
    if($adminRole==1){
        $sqlAccesses = "SELECT `fldMenuKey` FROM `tbl_admin_menu` WHERE `fldParentMenuKey`!=0 AND `fldMenuStatus`='active'";
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accessesArr = mysqli_fetch_all($qryAccesses,MYSQLI_ASSOC);
            $accesses=implode(",",array_column($accessesArr, 'fldMenuKey'));
        }
    }else{
        $sqlAccesses = "SELECT `fldRoleAccesses` FROM `tbl_admin_roles` WHERE `fldRoleStatus`='active' AND `fldRoleKey`=" . $adminRole;
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accesses = mysqli_fetch_assoc($qryAccesses)["fldRoleAccesses"];
        }
    }
    
    if($accesses==""){
        $returnData["status"]="warning";
        $returnData["message"]="Menu not found";
        return $returnData;
        exit();
    }
    

    $parentMenuKeysArr=[];
    $sqlParentMenuKeys = "SELECT `fldParentMenuKey` FROM `tbl_admin_menu` WHERE `fldMenuKey` IN (".$accesses.") AND `fldMenuStatus`='active' GROUP BY `fldParentMenuKey`;";
    if ($qryParentMenuKeys = mysqli_query($dbCon, $sqlParentMenuKeys)) {
        if (mysqli_num_rows($qryParentMenuKeys)>0) {
            while($row=mysqli_fetch_assoc($qryParentMenuKeys)){
                $parentMenuKeysArr[]=$row["fldParentMenuKey"];
            }
        }
    }

    $parentMenuKeys=implode(",",$parentMenuKeysArr);
    $sqlMenuList="SELECT * FROM `tbl_admin_menu` WHERE `fldMenuKey` IN (".$parentMenuKeys.") AND `fldMenuStatus`='active' ORDER BY `fldMenuOrderBy` ASC";
    if($qryMenuList=mysqli_query($dbCon,$sqlMenuList)){
        if (mysqli_num_rows($qryMenuList)>0) {
            $returnData["status"]="success";
            $returnData["message"]="Menu fetched success";
            $menuLoop=-1;
            while($rowMenuList=mysqli_fetch_assoc($qryMenuList)){
                $menuLoop++;
                $parentMenuKey=$rowMenuList["fldMenuKey"];
                $returnData["data"][$menuLoop]["menuLabel"]=$rowMenuList["fldMenuLabel"];
                $returnData["data"][$menuLoop]["menuIcon"]=$rowMenuList["fldMenuIcon"];
                $returnData["data"][$menuLoop]["menuFile"]=$rowMenuList["fldMenuFile"];
                $returnData["data"][$menuLoop]["subMenus"]=[];

                $sqlSubMenuList="SELECT * FROM `tbl_admin_menu` WHERE `fldMenuKey` IN (".$accesses.") AND `fldMenuStatus`='active' AND `fldParentMenuKey`=".$parentMenuKey." ORDER BY `fldMenuOrderBy` ASC";
                if ($qrySubMenuList = mysqli_query($dbCon, $sqlSubMenuList)) {
                    if (mysqli_num_rows($qrySubMenuList)>0) {
                        $subMenuLoop=-1;
                        while($rowSubMenuList=mysqli_fetch_assoc($qrySubMenuList)){
                            $subMenuLoop++;
                            $returnData["data"][$menuLoop]["subMenus"][$subMenuLoop]["menuLabel"]=$rowSubMenuList["fldMenuLabel"];
                            $returnData["data"][$menuLoop]["subMenus"][$subMenuLoop]["menuIcon"]=$rowSubMenuList["fldMenuIcon"];
                            $returnData["data"][$menuLoop]["subMenus"][$subMenuLoop]["menuFile"]=$rowSubMenuList["fldMenuFile"];
                        }
                    }else{
                        $returnData["status"]="warning";
                        $returnData["message"]="Sub Menu not found";
                    }
                }else{
                    $returnData["status"]="error";
                    $returnData["message"]="Sub Menu fetched failed";
                }
            }
        }else{
            $returnData["status"]="warning";
            $returnData["message"]="Menu not found";
        }
    }else{
        $returnData["status"]="error";
        $returnData["message"]="Menu fetched failed";
    }
    return $returnData;
}


function ForgotAdministratorUser($POST){
    global $dbCon;
    $returnData=[];
    $adminPassword=rand(0000,999999);
    $sql="SELECT * FROM `tbl_admin_details` WHERE `fldAdminEmail`='".$POST["email"]."' AND `fldAdminStatus`='active'";
    if($result=mysqli_query($dbCon,$sql)){
        if(mysqli_num_rows($result)>0){
            $row=mysqli_fetch_assoc($result);
            $ins = "UPDATE `tbl_admin_details` 
                        SET 
                            `fldAdminPassword`='" . $adminPassword . "'
                         WHERE `fldAdminKey`='" . $row['fldAdminKey'] . "'";

            if (mysqli_query($dbCon, $ins)) {
                $to=$POST["email"];
                $sub='Password Forgot successfull';
                $user_name=$row['fldAdminName'];
                $url=SERVICE_URL;
                $user_id=$POST['email'];
                $password=$adminPassword;
                $msg='Hey <b>'.$user_name.',</b><br>
                Your Password Forgot successfull!<br>
                To get started, here is your new login credentials:<br>
                <b>Url:</b> '.$url.'<br>
                <b>User Id:</b> '.$user_id.'<br>
                <b>Password:</b> '.$password.'';
                $emailReturn=SendMailByMySMTPmailTemplate($to,$sub,$msg,$tmpId=null);
                if($emailReturn==true){
                    $returnData['status'] = "success";
                    $returnData['message'] = "Password Forgot successfull. Check Your Email. ";
                } else {
                    $returnData["status"]="warning";
                    $returnData["message"]="Something went wrong, Try again...!";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Password Forgot failed";
            }
        }else{
            $returnData["status"]="warning";
            $returnData["message"]="Invalid Credentials, Try again...!";
        }
    }else{
        $returnData["status"]="warning";
        $returnData["message"]="Something went wrong, Try again...!";
    }
    return $returnData;
}

function loginAdministratorUser($POST){
    global $dbCon;
    $returnData=[];
    $sql="SELECT * FROM `erp_bug_user_details` WHERE `fldAdminEmail`='".$POST["email"]."' AND `fldAdminStatus`='active'";
    if($result=mysqli_query($dbCon,$sql)){
        if(mysqli_num_rows($result)>0){
            $row=mysqli_fetch_assoc($result);
            if($POST["pass"]==$row["fldAdminPassword"]){
                $_SESSION["logedAdminInfo"]["adminId"]=$row["fldAdminKey"];
                $_SESSION["logedAdminInfo"]["adminName"]=$row["fldAdminName"];
                $_SESSION["logedAdminInfo"]["adminEmail"]=$row["fldAdminEmail"];
                $_SESSION["logedAdminInfo"]["adminPhone"]=$row["fldAdminPhone"];
                // $_SESSION["logedAdminInfo"]["adminRole"]=$row["fldAdminRole"];
                $_SESSION["logedAdminInfo"]["adminType"]=$row["user_type"];
                $returnData["status"]="success";
                $returnData["message"]="Login success";
            }else{
                $returnData["status"]="warning";
                $returnData["message"]="Invalid Password, Try again...!";
            }
        }else{
            $returnData["status"]="warning";
            $returnData["message"]="Invalid Credentials, Try again...!";
        }
    }else{
        $returnData["status"]="warning";
        $returnData["message"]="Something went wrong, Try again...!";
    }
    return $returnData;
}

function saveAdministratorSettings($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "title" => "required",
        "timeZone" => "required",
        "email" => "required|email",
        "phone" => "required",
        "address" => "required",
        "logo" => "required|array",
        "favicon" => "required|array",
        "footer" => "required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }


    $sql = [];

    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["title"]."' WHERE `fldSettingName`='title'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["timeZone"]."' WHERE `fldSettingName`='timeZone'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["email"]."' WHERE `fldSettingName`='email'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["phone"]."' WHERE `fldSettingName`='phone'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["address"]."' WHERE `fldSettingName`='address'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$POST["footer"]."' WHERE `fldSettingName`='footer'");

    $logoObj=uploadFile($POST["logo"], "../public/storage/logo/",["jpg","png","ico","jpeg"]);
    $faviconObj=uploadFile($POST["favicon"], "../public/storage/logo/",["jpg","png","ico","jpeg"]);
    $prevLogo=$prevFavicon="";
    if($logoObj["status"]=="success"){
        $prevLogo=getAdministratorSettings("logo");
        if($prevLogo!=""){
            $prevLogo ="../public/storage/logo/".$prevLogo;
        }

        array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$logoObj["data"]."' WHERE `fldSettingName`='logo'");
    }
    if($faviconObj["status"]=="success"){
        $prevFavicon=getAdministratorSettings("favicon");
        if($prevFavicon!=""){
            $prevFavicon ="../public/storage/logo/".$prevFavicon;
        }
        array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='".$faviconObj["data"]."' WHERE `fldSettingName`='favicon'");
    }


    if (mysqli_multi_query($dbCon, implode(";", $sql))) {
        $returnData["status"] = "success";
        $returnData["message"] = "Settings saved successfully.";

        if($prevLogo!=""){
            unlink($prevLogo);
        }
        if($prevFavicon!=""){
            unlink($prevFavicon);
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Settings saved failed!";
    }

    return $returnData;
}

function getAdministratorSettings($settingName=""){
    global $dbCon;

    $sql = "SELECT * FROM `tbl_admin_settings` WHERE `fldSettingName`='" .$settingName. "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        $row = mysqli_fetch_assoc($res);
        return $row["fldSettingValue"];
    }
    return "";
}



?>
