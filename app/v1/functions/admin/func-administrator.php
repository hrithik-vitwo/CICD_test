<?php

function administratorAuth()
{
    global $dbCon;
    $commonAccessPages = ["index.php", "empty-page.php", "empty-form.php"];
    if (!isset($_SESSION["logedAdminInfo"]["adminId"]) || !isset($_SESSION["logedAdminInfo"]["adminRole"])) {
        redirect(ADMIN_URL . "login.php");
    } else {
        $adminRole = $_SESSION["logedAdminInfo"]["adminRole"];
        if ($adminRole != 1) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            if (!in_array($currentPage, $commonAccessPages)) {
                $sqlAccesses = "SELECT `fldRoleAccesses` FROM `tbl_admin_roles` WHERE `fldRoleStatus`='active' AND `fldRoleKey`=" . $adminRole;
                $accesses = "";
                if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
                    $accesses = mysqli_fetch_assoc($qryAccesses)["fldRoleAccesses"];
                } else {
                    redirect(ADMIN_URL);
                }
                $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_admin_menu` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldMenuFile`='".$currentPage."'";
                if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                    if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                        redirect(ADMIN_URL);
                    }
                } else {
                    redirect(ADMIN_URL);
                }
            }
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
                $url=ADMIN_URL;
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
    $sql="SELECT * FROM `tbl_admin_details` WHERE `fldAdminEmail`='".$POST["email"]."' AND `fldAdminStatus`='active'";
    if($result=mysqli_query($dbCon,$sql)){
        if(mysqli_num_rows($result)>0){
            $row=mysqli_fetch_assoc($result);
            if($POST["pass"]==$row["fldAdminPassword"]){
                $_SESSION["logedAdminInfo"]["adminId"]=$row["fldAdminKey"];
                $_SESSION["logedAdminInfo"]["adminName"]=$row["fldAdminName"];
                $_SESSION["logedAdminInfo"]["adminEmail"]=$row["fldAdminEmail"];
                $_SESSION["logedAdminInfo"]["adminPhone"]=$row["fldAdminPhone"];
                $_SESSION["logedAdminInfo"]["adminRole"]=$row["fldAdminRole"];
                $_SESSION["logedAdminInfo"]["adminType"]='admin';
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


function addAdministratorMenu($POST)
{
    global $dbCon;
    $menuLabel = $POST['menuLabel'];
    $menuFile = $POST['menuFile'];
    $menuIcon = $POST['menuIcon'];
    $menuOrderBy = $POST['menuOrderBy'];

    $ins = "INSERT INTO `tbl_admin_menu`
                SET
                    `fldMenuLabel` = '" . $menuLabel . "',
                    `fldMenuIcon` = '" . $menuIcon . "',
                    `fldMenuFile` = '" . $menuFile . "',
                    `fldMenuOrderBy` = '" . $menuOrderBy . "'
    ";
    if (mysqli_query($dbCon, $ins)) {
        $returnData["status"] = "success";
        $returnData["message"] = "Menu added successfully.";
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Menu added failed!";
    }
    return $returnData;
}
function updateAdministratorMenu($POST)
{
    global $dbCon;
    $menuKey = $POST["menuKey"];
    $parentMenuKey = $POST["parentMenuKey"];
    $menuLabel = $POST['menuLabel'];
    $menuFile = $POST['menuFile'];
    $menuIcon = $POST['menuIcon'];
    $menuOrderBy = $POST['menuOrderBy'];
    $menuStatus = $POST['menuStatus'];

    if ($res = mysqli_query($dbCon, "SELECT COUNT(`fldMenuLabel`) AS 'noOfRecords' FROM `tbl_admin_menu` WHERE `fldMenuKey`=" . $POST["menuKey"])) {
        if (mysqli_fetch_assoc($res)["noOfRecords"] == 1) {
            $ins = "UPDATE `tbl_admin_menu`
                SET
                    `fldMenuLabel` = '" . $menuLabel . "',
                    `fldMenuIcon` = '" . $menuIcon . "',
                    `fldMenuFile` = '" . $menuFile . "',
                    `fldMenuOrderBy` = '" . $menuOrderBy . "',
                    `fldParentMenuKey` = '" . $parentMenuKey . "',
                    `fldMenuStatus` = '" . $menuStatus . "'
                WHERE 
                    `fldMenuKey`=" . $menuKey;
            if (mysqli_query($dbCon, $ins)) {
                $returnData["status"] = "success";
                $returnData["message"] = "Menu modified successfully.";
            } else {
                $returnData["status"] = "warning";
                $returnData["message"] = "Menu modify failed!";
            }
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Invalid menu id, menu not found!";
        }
    }


    return $returnData;
}

function addAdministratorSubMenu($POST)
{
    global $dbCon;
    $menuKey = $POST['menuKey'];
    $subMenuLabel = $POST['subMenuLabel'];
    $subMenuFile = $POST['subMenuFile'];
    $subMenuIcon = $POST['subMenuIcon'];
    $subMenuOrderBy = $POST['subMenuOrderBy'];

    $ins = "INSERT INTO `tbl_admin_menu` 
                SET
                    `fldMenuLabel` = '" . $subMenuLabel . "',
                    `fldParentMenuKey` = '" . $menuKey . "',
                    `fldMenuIcon` = '" . $subMenuIcon . "',
                    `fldMenuFile` = '" . $subMenuFile . "',
                    `fldMenuOrderBy` = '" . $subMenuOrderBy . "'";

    if (mysqli_query($dbCon, $ins)) {
        $returnData["status"] = "success";
        $returnData["message"] = "Sub-Menu added successfully.";
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Sub-Menu added failed!";
    }
    return $returnData;
}

function getAdministratorMenuList($parentMenuKey = 0)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_admin_menu` WHERE `fldParentMenuKey`=" . $parentMenuKey . " AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

function getAdministratorMenuDetails($menuKey = 0)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_admin_menu` WHERE `fldMenuKey`=" . $menuKey;
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}


// Administrator Role
function addNewAdministratorRole($POST = [])
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "roleName" => "required",
        "menuFiles" => "required|array"
    ], [
        "roleName" => "Please enter Admin Role name",
        "menuFiles" => "Please select at least one access"
    ]);

    if ($isValidate["status"] == "success") {

        $roleName = $POST["roleName"];
        $roleAccesses = implode(",", $POST["menuFiles"]);
        $roleDescription = $POST["roleDescription"];

        $sql = "SELECT * FROM `tbl_admin_roles` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleStatus`!='deleted'";

        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_admin_roles` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleAccesses`='" . $roleAccesses . "',
                            `fldRoleDescription`='" . $roleDescription . "'
                ";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin role added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin role added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Role name already exist";
            }
        } else {
            $returnData['status'] = "danger";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "danger";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

function updateAdministratorRole($POST = [])
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "editKey" => "required",
        "roleName" => "required",
        "menuFiles" => "required|array"
    ], [
        "editKey" => "Invalid role key",
        "roleName" => "Please enter Admin Role name",
        "menuFiles" => "Please select at least one access"
    ]);

    if ($isValidate["status"] == "success") {
        $roleKey = $POST["editKey"];
        $roleName = $POST["roleName"];
        $roleAccesses = implode(",", $POST["menuFiles"]);
        $roleDescription = $POST["roleDescription"];

        $sql = "SELECT * FROM `tbl_admin_roles` WHERE `fldRoleKey`=" . $roleKey . " AND `fldRoleStatus`!='deleted'";

        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {

                $checkName = "SELECT * FROM `tbl_admin_roles` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleKey`!=" . $roleKey . " AND  `fldRoleStatus`!='deleted'";
                if ($resCheckName = mysqli_query($dbCon, $checkName)) {
                    if (mysqli_num_rows($resCheckName) > 0) {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Role name already exists, try another role name!";
                        return $returnData;
                    }
                }
                $ins = "UPDATE `tbl_admin_roles` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleAccesses`='" . $roleAccesses . "',
                            `fldRoleDescription`='" . $roleDescription . "' WHERE `fldRoleKey`=" . $roleKey;

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Role updated success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Role update failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Role does't exist";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

function getAdministratorRoleDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_admin_roles` WHERE `fldRoleStatus`!='deleted' AND `fldRoleKey`=" . $key . "";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

function getAllAdministratorRoles()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_admin_roles` WHERE `fldRoleStatus`!='deleted'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

// End Administrator Role




// Administrator User
function getAllAdministratorUsers()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_admin_details`,`tbl_admin_roles` WHERE `tbl_admin_details`.`fldAdminRole`=`tbl_admin_roles`.`fldRoleKey` AND `tbl_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_admin_roles`.`fldRoleStatus`!='deleted'";

    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

function getAdministratorUserDetails($key = null)
{
    global $dbCon;

    $sql = "SELECT `tbl_admin_details`.*, `tbl_admin_roles`.`fldRoleName` FROM `tbl_admin_details`,`tbl_admin_roles` WHERE `tbl_admin_details`.`fldAdminRole`=`tbl_admin_roles`.`fldRoleKey` AND `tbl_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_admin_roles`.`fldRoleStatus`!='deleted' AND `fldAdminKey`=" . $key;

    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}
function updateAdministratorUserDetails($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminKey" => "required",
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:8",
        "adminRole" => "required",
    ], [
        "adminKey" => "Invalid admin",
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:8 character)",
        "adminRole" => "Select a role",
    ]);

    if ($isValidate["status"] == "success") {

        $adminKey = $POST["adminKey"];
        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $adminRole = $POST["adminRole"];

        $sql = "SELECT * FROM `tbl_admin_details` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `tbl_admin_details` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "' WHERE `fldAdminKey`='" . $adminKey . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin modified success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin modified failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin not exist";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

function addNewAdministratorUser($POST = [])
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:8",
        "adminRole" => "required",
        "adminAvatar" => "required"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:8 character)",
        "adminRole" => "Select a role",
        "adminAvatar" => "Select a avatar/photo"
    ]);

    if ($isValidate["status"] == "success") {

        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $adminRole = $POST["adminRole"];

        $adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `tbl_admin_details` WHERE `fldAdminEmail`='" . $adminEmail . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_admin_details` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "',
                                `fldAdminAvatar`='" . $adminAvatar["data"] . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin already exist";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}
// End Administrator User

function getAdministratorAccessesNames($roleAccesses = "0")
{
    global $dbCon;
    $returnData = [];
    if ($roleAccesses == "0") {
        $returnData["status"] = "success";
        $returnData["message"] = "success";
        $returnData["data"] = "All";
    } else {
        $sql = "SELECT `fldMenuLabel` FROM `tbl_admin_menu` WHERE `fldMenuKey` IN (" . $roleAccesses . ") AND `fldMenuStatus`='active'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData["status"] = "success";
                $returnData["message"] = "success";

                $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
                $returnData["data"] = implode(', ', array_column($data, 'fldMenuLabel'));
            } else {
                $returnData["status"] = "warning";
                $returnData["message"] = "Not found";
                $returnData["data"] = "";
            }
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Not found";
            $returnData["data"] = "";
        }
    }
    return $returnData;
}


function administratorFuncUpdateQry($table = "", $fieldsAndData = [], $conditions = "")
{
    global $dbCon;

    $fields = "";
    foreach ($fieldsAndData as $key => $data) {
        if ($fields == "") {
            $fields += "`" . $key . "`='" . $data . "'";
        } else {
            $fields += ", `" . $key . "`='" . $data . "'";
        }
    }
    if ($table != "" && $fields != "" && $conditions != "") {
        $sql = "UPDATE `" . $table . "` SET " . $fields . " WHERE " . $conditions;
        if (mysqli_query($dbCon, $sql)) {
            $returnData["status"] = "success";
            $returnData["status"] = "Data modified success";
        } else {
            $returnData["status"] = "warning";
            $returnData["status"] = "Data modified failed!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["status"] = "Data modified failed!";
    }
    return $returnData;
}

function administratorFuncChangeStatus($data = [], $tableName = "", $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $returnData["status"] = null;
    $returnData["message"] = null;
    if (!empty($data)) {
        $id = isset($data["id"]) ? $data["id"] : 0;
        $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
        $prevExeQuery = mysqli_query($dbCon, $prevSql);
        $prevNumRecords = mysqli_num_rows($prevExeQuery);
        if ($prevNumRecords > 0) {
            $prevData = mysqli_fetch_assoc($prevExeQuery);
            $newStatus = "deleted";
            if ($data["changeStatus"] == "active_inactive") {
                $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
            }
            $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
            if (mysqli_query($dbCon, $changeStatusSql)) {
                $returnData["status"] = "success";
                $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
            } else {
                $returnData["status"] = "error";
                $returnData["message"] = "Something went wrong, Try again...!";
            }
            $returnData["changeStatusSql"] = $changeStatusSql;
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Something went wrong, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Please provide all valid data...!";
    }
    return $returnData;
}
