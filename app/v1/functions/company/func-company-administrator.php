<?php
function administratorAuth()
{
    global $dbCon;
    $commonAccessPages = ["index.php", "empty-page.php", "empty-form.php"];
    if (!isset($_SESSION["logedCompanyAdminInfo"]["adminId"]) || !isset($_SESSION["logedCompanyAdminInfo"]["adminRole"])) {
        redirect(COMPANY_URL . "login.php");
    } else {
        $adminRole = $_SESSION["logedCompanyAdminInfo"]["adminRole"];
        if ($adminRole != 3) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            if (!in_array($currentPage, $commonAccessPages)) {
                $sqlAccesses = "SELECT `grandParent`,`subChild`,`subParent` FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleStatus`='active' AND `fldRoleKey`=" . $adminRole;
                $accesses = "";
                $qryAccesses=queryGet($sqlAccesses);
                if ($qryAccesses['status']='success') {
                    $accesses1 =  $qryAccesses['data']["subParent"];
                    $accesses2 =  $qryAccesses['data']["grandParent"];
                    $accesses3 = $qryAccesses['data']["subChild"];
                    $accesses=$accesses1.','.$accesses2.','.$accesses3;
                } else {
                redirect(COMPANY_URL);
                }
                $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_company_admin_menu` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";
                if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                    if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                        redirect(COMPANY_URL);
                    }
                } else {
                    redirect(COMPANY_URL);
                }
            }
        }
    }
}


function getAdministratorMenuSubMenu()
{
    global $dbCon;
    $returnData = [];
    $accesses = "";
    $adminRole = $_SESSION["logedCompanyAdminInfo"]["adminRole"];
    if ($adminRole == 3) {
        $sqlAccesses = "SELECT `fldMenuKey` FROM `tbl_branch_admin_menu` WHERE `fldParentMenuKey`!='-0' AND `menuFor`='company' AND `visibility`='yes' AND `fldMenuStatus`='active'";
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accessesArr = mysqli_fetch_all($qryAccesses, MYSQLI_ASSOC);
            $accesses = implode(",", array_column($accessesArr, 'fldMenuKey'));
        }
    } else {
        $sqlAccesses = "SELECT `fldRoleAccesses` FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleStatus`='active' AND `fldRoleKey`=" . $adminRole;
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accesses = mysqli_fetch_assoc($qryAccesses)["fldRoleAccesses"];
        }
    }

    if ($accesses == "") {
        $returnData["status"] = "warning";
        $returnData["message"] = "Menu not found";
        return $returnData;
        exit();
    }

    $grandParentMenuKeysArr = [];
    $sqlgrandParentMenuKeys = "SELECT `fldGrandParentMenu` FROM `tbl_branch_admin_menu` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' GROUP BY `fldGrandParentMenu`;";
    if ($qrygrandParentMenuKeys = mysqli_query($dbCon, $sqlgrandParentMenuKeys)) {
        if (mysqli_num_rows($qrygrandParentMenuKeys) > 0) {
            while ($row = mysqli_fetch_assoc($qrygrandParentMenuKeys)) {
                $grandParentMenuKeysArr[] = $row["fldGrandParentMenu"];
            }
        }
    }


    $grandParentMenuKeys = implode(",", $grandParentMenuKeysArr);

    $parentMenuKeysArr = [];
    $sqlParentMenuKeys = "SELECT `fldParentMenuKey` FROM `tbl_branch_admin_menu` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' GROUP BY `fldParentMenuKey`;";
    if ($qryParentMenuKeys = mysqli_query($dbCon, $sqlParentMenuKeys)) {
        if (mysqli_num_rows($qryParentMenuKeys) > 0) {
            while ($row = mysqli_fetch_assoc($qryParentMenuKeys)) {
                $parentMenuKeysArr[] = $row["fldParentMenuKey"];
            }
        }
    }

    $parentMenuKeys = implode(",", $parentMenuKeysArr);

    $sqlGrandMenuList = "SELECT * FROM `tbl_branch_admin_menu` WHERE `fldMenuKey` IN (" . $grandParentMenuKeys . ") AND `fldMenuStatus`='active' ORDER BY `fldMenuOrderBy` ASC";
    if ($qryGrandMenuList = mysqli_query($dbCon, $sqlGrandMenuList)) {
        if (mysqli_num_rows($qryGrandMenuList) > 0) {
            $returnData["status"] = "success";
            $returnData["message"] = "Menu fetched success";
            $grandmenuLoop = -1;
            while ($rowGrandMenuList = mysqli_fetch_assoc($qryGrandMenuList)) {
                $grandmenuLoop++;
                $grandParentMenuKey = $rowGrandMenuList["fldMenuKey"];
                $returnData["data"][$grandParentMenuKey]["menuLabel"] = $rowGrandMenuList["fldMenuLabel"];
                $returnData["data"][$grandParentMenuKey]["menuIcon"] = $rowGrandMenuList["fldMenuIcon"];
                $returnData["data"][$grandParentMenuKey]["extraPrefixFolder"] = $rowGrandMenuList["extraPrefixFolder"];
                $returnData["data"][$grandParentMenuKey]["menuFile"] = $rowGrandMenuList["fldMenuFile"];
                $returnData["data"][$grandParentMenuKey]["sidebar_view"] = $rowGrandMenuList["sidebar_view"];
                $returnData["data"][$grandParentMenuKey]["visibility"] = $rowGrandMenuList["visibility"];
                $returnData["data"][$grandParentMenuKey]["subParentMenus"] = [];

                $sqlMenuList = "SELECT * FROM `tbl_branch_admin_menu` WHERE `fldMenuKey` IN (" . $parentMenuKeys . ") AND `fldMenuStatus`='active' ORDER BY `fldMenuOrderBy` ASC";
                if ($qryMenuList = mysqli_query($dbCon, $sqlMenuList)) {
                    if (mysqli_num_rows($qryMenuList) > 0) {
                        $menuLoop = -1;
                        while ($rowMenuList = mysqli_fetch_assoc($qryMenuList)) {
                            $menuLoop++;
                            $parentMenuKey = $rowMenuList["fldMenuKey"];
                            $sqlSubMenuList = "SELECT * FROM `tbl_branch_admin_menu` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldParentMenuKey`=" . $parentMenuKey . " AND `fldGrandParentMenu`=" . $grandParentMenuKey . " ORDER BY `fldMenuOrderBy` ASC";
                            if ($qrySubMenuList = mysqli_query($dbCon, $sqlSubMenuList)) {
                                if (mysqli_num_rows($qrySubMenuList) > 0) {
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuLabel"] = $rowMenuList["fldMenuLabel"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuIcon"] = $rowMenuList["fldMenuIcon"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["extraPrefixFolder"] = $rowMenuList["extraPrefixFolder"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuFile"] = $rowMenuList["fldMenuFile"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"] = [];

                                    $subMenuLoop = -1;
                                    while ($rowSubMenuList = mysqli_fetch_assoc($qrySubMenuList)) {
                                        $subMenuLoop++;
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuLabel"] = $rowSubMenuList["fldMenuLabel"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuIcon"] = $rowSubMenuList["fldMenuIcon"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["extraPrefixFolder"] = $rowSubMenuList["extraPrefixFolder"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuFile"] = $rowSubMenuList["fldMenuFile"];
                                    }
                                }
                            } else {
                                $returnData["status"] = "error";
                                $returnData["message"] = "Sub Menu fetched failed";
                            }
                        }
                    } else {
                        $returnData["status"] = "warning";
                        $returnData["message"] = "Menu not found";
                    }
                } else {
                    $returnData["status"] = "error";
                    $returnData["message"] = "Menu fetched failed";
                }
            }
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Grand Menu not found";
        }
    } else {
        $returnData["status"] = "error";
        $returnData["message"] = "Grand Menu fetched failed";
    }
    return $returnData;
}
function ForgotAdministratorUser($POST)
{
    global $dbCon;
    $returnData = [];
    $adminPassword = rand(0000, 999999);
    $sql = "SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminEmail`='" . $POST["email"] . "' AND `fldAdminStatus`='active'";
    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $ins = "UPDATE `tbl_company_admin_details` 
                        SET 
                            `fldAdminPassword`='" . $adminPassword . "'
                         WHERE `fldAdminKey`='" . $row['fldAdminKey'] . "'";

            if (mysqli_query($dbCon, $ins)) {
                $to = $POST["email"];
                $sub = 'Password Forgot successfull';
                $user_name = $row['fldAdminName'];
                $url = COMPANY_URL;
                $user_id = $POST['email'];
                $password = $adminPassword;
                $msg = 'Hey <b>' . $user_name . ',</b><br>
                Your Password Forgot successfull!<br>
                To get started, here is your new login credentials:<br>
                <b>Url:</b> ' . $url . '<br>
                <b>User Id:</b> ' . $user_id . '<br>
                <b>Password:</b> ' . $password . '';
                $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);
                if ($emailReturn == true) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Password Forgot successfull. Check Your Email. ";
                } else {
                    $returnData["status"] = "warning";
                    $returnData["message"] = "Something went wrong, Try again...!";
                }
                $returnData['status'] = "success";
                $returnData['message'] = "Password Forgot successfull. Check Your Email. ";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Password Forgot failed";
            }
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Invalid Credentials, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Something went wrong, Try again...!";
    }
    return $returnData;
}


function loginAdministratorUser($POST)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminEmail`='" . $POST["email"] . "' AND `fldAdminStatus`='active'";
    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $result = checkStatus($row['fldAdminCompanyId']);
            if ($result['status'] == "active") {
                if ($POST["pass"] == $row["fldAdminPassword"]) {
                
                    $get_country = queryGet("SELECT * FROM `erp_companies` WHERE company_id = '".$row["fldAdminCompanyId"]."'");
                
                    $_SESSION["logedCompanyAdminInfo"]["companyCountry"] = $get_country['data']['company_country'];
                    $_SESSION["logedCompanyAdminInfo"]["adminId"] = $row["fldAdminKey"];
                    $_SESSION["logedCompanyAdminInfo"]["adminName"] = $row["fldAdminName"];
                    $_SESSION["logedCompanyAdminInfo"]["adminEmail"] = $row["fldAdminEmail"]; 
                    $_SESSION["logedCompanyAdminInfo"]["adminPhone"] = $row["fldAdminPhone"];
                    $_SESSION["logedCompanyAdminInfo"]["adminRole"] = $row["fldAdminRole"];
                    $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] = $row["fldAdminCompanyId"];
                    $_SESSION["logedCompanyAdminInfo"]["adminType"] = 'company';

                    
                    //check_country
                    $get_country = queryGet("SELECT * FROM `erp_companies` WHERE company_id = '".$row["fldAdminCompanyId"]."'");
                
                    $_SESSION["logedCompanyAdminInfo"]["companyCountry"] = $get_country['data']['company_country'];


                    $returnData["status"] = "success";
                    $returnData["message"] = "Login success";
                } else {
                    $returnData["status"] = "warning";
                    $returnData["message"] = "Invalid Password, Try again...!";
                }
            }
            else{
                $returnData["status"] = "warning";
                $returnData["message"] = "Invalid Credentials, Try again...!";
            }
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Invalid Credentials, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Something went wrong, Try again...!";
    }
    return $returnData;
}

function checkStatus($company_id)
{
    if (!$company_id) {
        return ['status' => 'inactive', 'message' => 'Missing company or branch ID'];
    }

    if (!queryGet("SELECT 1 FROM erp_companies WHERE company_id = '$company_id' AND company_status = 'active' LIMIT 1")['numRows']) {
        return ['status' => 'inactive', 'message' => 'Company inactive'];
    }

    return ['status' => 'active', 'message' => 'All active'];
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

    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["title"] . "' WHERE `fldSettingName`='title'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["timeZone"] . "' WHERE `fldSettingName`='timeZone'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["email"] . "' WHERE `fldSettingName`='email'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["phone"] . "' WHERE `fldSettingName`='phone'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["address"] . "' WHERE `fldSettingName`='address'");
    array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $POST["footer"] . "' WHERE `fldSettingName`='footer'");

    $logoObj = uploadFile($POST["logo"], COMP_STORAGE_DIR . "/profile/", ["jpg", "png", "ico", "jpeg"]);
    $faviconObj = uploadFile($POST["favicon"], COMP_STORAGE_DIR . "/profile/", ["jpg", "png", "ico", "jpeg"]);
    $prevLogo = $prevFavicon = "";
    if ($logoObj["status"] == "success") {
        $prevLogo = getAdministratorSettings("logo");
        if ($prevLogo != "") {
            $prevLogo = COMP_STORAGE_DIR . "/profile/" . $prevLogo;
        }

        array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $logoObj["data"] . "' WHERE `fldSettingName`='logo'");
    }
    if ($faviconObj["status"] == "success") {
        $prevFavicon = getAdministratorSettings("favicon");
        if ($prevFavicon != "") {
            $prevFavicon = COMP_STORAGE_DIR . "/profile/" . $prevFavicon;
        }
        array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $faviconObj["data"] . "' WHERE `fldSettingName`='favicon'");
    }


    if (mysqli_multi_query($dbCon, implode(";", $sql))) {
        $returnData["status"] = "success";
        $returnData["message"] = "Settings saved successfully.";

        if ($prevLogo != "") {
            unlink($prevLogo);
        }
        if ($prevFavicon != "") {
            unlink($prevFavicon);
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Settings saved failed!";
    }

    return $returnData;
}

function getAdministratorSettings($settingName = "")
{
    global $dbCon;

    $sql = "SELECT * FROM `tbl_admin_settings` WHERE `fldSettingName`='" . $settingName . "'";
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

    $ins = "INSERT INTO `tbl_company_admin_menu`
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

    if ($res = mysqli_query($dbCon, "SELECT COUNT(`fldMenuLabel`) AS 'noOfRecords' FROM `tbl_company_admin_menu` WHERE `fldMenuKey`=" . $POST["menuKey"])) {
        if (mysqli_fetch_assoc($res)["noOfRecords"] == 1) {
            $ins = "UPDATE `tbl_company_admin_menu`
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

    $ins = "INSERT INTO `tbl_company_admin_menu` 
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
    $sql = "SELECT * FROM `tbl_company_admin_menu` WHERE `fldParentMenuKey`=" . $parentMenuKey . " AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
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
    $sql = "SELECT * FROM `tbl_company_admin_menu` WHERE `fldMenuKey`=" . $menuKey;
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
    global $company_id;
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
        $roleDescription = $POST["roleDescription"];
        $fldRoleAccesses = $POST["fldRoleAccesses"];
        $grandParent = array();
        $subParent = array();
        $subChild = array();
        $subChildMin = array();
        foreach ($_POST["menuFiles"] as $menugrandParentKey => $menugrandParent) {
            $grandParent[] = $menugrandParentKey;
            foreach ($menugrandParent as $menuParentKey => $menuParent) {
                $subParent[] = $menuParentKey;
                foreach ($menuParent as $menuChildKey => $menuChild) {
                    $subChild[] = $menuChildKey;
                    $subChildMin[$menuChildKey] = $menuChild;
                }
            }
        }
        $grandParent = implode(',', $grandParent);
        $subParent = implode(',', $subParent);
        $subChild = implode(',', $subChild);
        $subChildMin = serialize($subChildMin);

        $fldRoleCompanyId = $company_id;



        $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleCompanyId`='" . $fldRoleCompanyId . "' AND `fldRoleAccesses`='Company' AND `fldRoleStatus`!='deleted'";

        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_branch_admin_roles_a2` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleAccesses`='" . $fldRoleAccesses . "',
                            `grandParent`='" . $grandParent . "',
                            `subParent`='" . $subParent . "',
                            `subChild`='" . $subChild . "',
                            `subChildMin`='" . $subChildMin . "',
                            `fldRoleDescription`='" . $roleDescription . "',
                            `fldRoleCompanyId`='" . $fldRoleCompanyId . "'";

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

// update adminstrator 
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
        $roleDescription = $POST["roleDescription"];
        $fldRoleAccesses = $POST["fldRoleAccesses"];
        $grandParent = array();
        $subParent = array();
        $subChild = array();
        $subChildMin = array();
        foreach ($_POST["menuFiles"] as $menugrandParentKey => $menugrandParent) {
            $grandParent[] = $menugrandParentKey;
            foreach ($menugrandParent as $menuParentKey => $menuParent) {
                $subParent[] = $menuParentKey;
                foreach ($menuParent as $menuChildKey => $menuChild) {
                    $subChild[] = $menuChildKey;
                    $subChildMin[$menuChildKey] = $menuChild;
                }
            }
        }
        $grandParent = implode(',', $grandParent);
        $subParent = implode(',', $subParent);
        $subChild = implode(',', $subChild);
        $subChildMin = serialize($subChildMin);

        $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleKey`=" . $roleKey . " AND `fldRoleAccesses`='Company' AND `fldRoleStatus`!='deleted'";

        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {

                $checkName = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleAccesses`='Company' AND `fldRoleKey`!=" . $roleKey . " AND  `fldRoleStatus`!='deleted'";
                if ($resCheckName = mysqli_query($dbCon, $checkName)) {
                    if (mysqli_num_rows($resCheckName) > 0) {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Role name already exists, try another role name!";
                        return $returnData;
                    }
                }
                $ins = "UPDATE `tbl_branch_admin_roles_a2` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleDescription`='" . $roleDescription . "'
                            `fldRoleAccesses`='" . $fldRoleAccesses . "',
                            `grandParent`='" . $grandParent . "',
                            `subParent`='" . $subParent . "',
                            `subChild`='" . $subChild . "',
                            `subChildMin`='" . $subChildMin . "' 
                        WHERE `fldRoleKey`=" . $roleKey;

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
    $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleKey`=" . $key . " AND `fldRoleAccesses`='Company'";
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
    global $company_id;
    $returnData = [];
    $fldRoleCompanyId =  $company_id;

    $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleStatus`!='deleted' AND (`fldRoleCompanyId`=" . $fldRoleCompanyId . " OR `fldRoleCompanyId`=0)";
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
    global $company_id;
    $fldAdminCompanyId = $company_id;
    $returnData = [];
    $sql = "SELECT tbl_company_admin_details.*," . ERP_COMPANIES . ".company_code," . ERP_COMPANIES . ".company_name,`tbl_branch_admin_roles_a2`.fldRoleName,`tbl_branch_admin_roles_a2`.fldRoleAccesses,`tbl_branch_admin_roles_a2`.grandParent,`tbl_branch_admin_roles_a2`.subParent,`tbl_branch_admin_roles_a2`.subChild FROM `tbl_company_admin_details`,`tbl_branch_admin_roles_a2`," . ERP_COMPANIES . " WHERE `tbl_company_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_company_admin_details`.`fldAdminCompanyId`=" . ERP_COMPANIES . ".`company_id` AND `tbl_company_admin_details`.`fldAdminCompanyId`='" . $fldAdminCompanyId . "' AND `tbl_company_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted'";
    $returnData = queryGet($sql, true);

    return $returnData;
}

// Administrator User
function getBranchAllAdministratorUsers()
{
    global $dbCon;
    global $company_id;
    $fldAdminCompanyId = $company_id;
    $returnData = [];
    $sql = "SELECT `tbl_branch_admin_details`.*," . ERP_COMPANIES . ".company_code," . ERP_COMPANIES . ".company_name, " . ERP_BRANCHES . ".branch_code," . ERP_BRANCHES . ".state,`tbl_branch_admin_roles_a2`.fldRoleName,`tbl_branch_admin_roles_a2`.fldRoleAccesses,`tbl_branch_admin_roles_a2`.grandParent,`tbl_branch_admin_roles_a2`.subParent,`tbl_branch_admin_roles_a2`.subChild FROM `tbl_branch_admin_details`," . ERP_COMPANIES . "," . ERP_BRANCHES . ",`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminCompanyId`=" . ERP_COMPANIES . ".`company_id` AND `tbl_branch_admin_details`.`fldAdminBranchId`=" . ERP_BRANCHES . ".`branch_id` AND `tbl_branch_admin_details`.`fldAdminCompanyId`='" . $fldAdminCompanyId . "' AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND (`tbl_branch_admin_details`.`fldAdminBranchLocationId` IS NULL OR  `tbl_branch_admin_details`.`fldAdminBranchLocationId`='' )AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted'";
    $returnData = queryGet($sql, true);

    return $returnData;
}
// Administrator User
function getLocationAllAdministratorUsers()
{
    global $dbCon;
    global $company_id;
    $fldAdminCompanyId = $company_id;
    $returnData = [];
    $sql = "SELECT `tbl_branch_admin_details`.*," . ERP_COMPANIES . ".company_code," . ERP_COMPANIES . ".company_name," . ERP_BRANCHES . ".branch_code," . ERP_BRANCHES . ".state," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_code," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_location,`tbl_branch_admin_roles_a2`.fldRoleName,`tbl_branch_admin_roles_a2`.fldRoleAccesses,`tbl_branch_admin_roles_a2`.grandParent,`tbl_branch_admin_roles_a2`.subParent,`tbl_branch_admin_roles_a2`.subChild FROM `tbl_branch_admin_details`," . ERP_COMPANIES . "," . ERP_BRANCHES . "," . ERP_BRANCH_OTHERSLOCATION . ",`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminCompanyId`=" . ERP_COMPANIES . ".`company_id` AND `tbl_branch_admin_details`.`fldAdminBranchId`=" . ERP_BRANCHES . ".`branch_id` AND `tbl_branch_admin_details`.`fldAdminBranchLocationId`=" . ERP_BRANCH_OTHERSLOCATION . ".`othersLocation_id` AND `tbl_branch_admin_details`.`fldAdminCompanyId`='" . $fldAdminCompanyId . "' AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND (`tbl_branch_admin_details`.`fldAdminBranchLocationId` IS NOT NULL)AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted'";
    $returnData = queryGet($sql, true);

    return $returnData;
}

function getAdministratorUserDetails($key = null)
{
    global $dbCon;

    $sql = "SELECT `tbl_branch_admin_details`.*, `tbl_branch_admin_roles_a2`.`fldRoleName` FROM `tbl_company_admin_details`,`tbl_branch_admin_roles_a2` WHERE `tbl_company_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_company_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted' AND `fldAdminKey`=" . $key;

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

        $sql = "SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `tbl_company_admin_details` 
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



function addNewLocationAdministratorUser($POST = [])
{
    global $dbCon;
    global $company_id;
    global $companyNameNav;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:2",
        "menuFiles" => "required|array"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:2 character)",
        "menuFiles" => "Please select at least one access"
    ]);

    if ($isValidate["status"] == "success") {

        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $fldRoleAccesses = $POST["fldRoleAccesses"];
        $userName = $POST["userName"] . rand(11, 99);
        $adminRole = $POST["adminRole"];
        $bandLocatinId = explode('|', $POST["fldAdminBranchLocationId"]);
        $location_id = $bandLocatinId[0];
        $branch_id = $bandLocatinId[1];
        $locationName = $bandLocatinId[2];
        $adminDesignation = '';
        $adminAvatar = '';
        if (isset($POST["adminAvatar"])) {
            $adminAvatar = uploadFile($POST["adminAvatar"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png"])['data'];
        }

        $roleName = "Custom Location User " . $userName;
        $roleDescription = "Custom Location User For" . $adminName . '(' . $userName . ')';
        // $fldRoleAccesses = 'Location';
        $grandParent = array();
        $subParent = array();
        $subChild = array();
        $subChildMin = array();
        foreach ($_POST["menuFiles"] as $menugrandParentKey => $menugrandParent) {
            $grandParent[] = $menugrandParentKey;
            foreach ($menugrandParent as $menuParentKey => $menuParent) {
                $subParent[] = $menuParentKey;
                foreach ($menuParent as $menuChildKey => $menuChild) {
                    $subChild[] = $menuChildKey;
                    $subChildMin[$menuChildKey] = $menuChild;
                }
            }
        }
        $grandParent = implode(',', $grandParent);
        $subParent = implode(',', $subParent);
        $subChild = implode(',', $subChild);
        $subChildMin = serialize($subChildMin);


        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminUserName`='" . $userName . "'";
        $res = queryGet($sql);
        if ($res['status'] != 'success') {
            $insRoles = "INSERT INTO `tbl_branch_admin_roles_a2` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleAccesses`='" . $fldRoleAccesses . "',
                            `grandParent`='" . $grandParent . "',
                            `subParent`='" . $subParent . "',
                            `subChild`='" . $subChild . "',
                            `subChildMin`='" . $subChildMin . "',
                            `fldRoleDescription`='" . $roleDescription . "',
                            `fldRoleCompanyId`='" . $company_id . "',
                            `fldAdminBranchId`='" . $branch_id . "',
                            `fldAdminLocationId`='$location_id'";
            $getResponce = queryInsert($insRoles);
            $lastId = $getResponce['insertedId'];
            $insUser = "INSERT INTO `tbl_branch_admin_details` 
                                SET 
                                    `fldAdminCompanyId`='$company_id',
                                    `fldAdminBranchId`='$branch_id',
                                    `fldAdminBranchLocationId`='$location_id',
                                    `user_type`='$fldRoleAccesses',
                                    `fldAdminName`='$adminName',
                                    `fldAdminEmail`='$adminEmail',
                                    `fldAdminPhone`='$adminPhone',
                                    `fldAdminPassword`='$adminPassword',
                                    `fldAdminUserName` = '$userName',
                                    `flAdminDesignation` = '$adminDesignation',
                                    `fldAdminAvatar` = '$adminAvatar',
                                    `fldAdminRole`=$lastId";
            $getRes = queryInsert($insUser);
            if ($getRes['status'] == 'success') {
                $sub = "Congratulations on the Successful Launch of $locationName Location!";
                $msg = "Dear " . $adminName . ",<br>           
                        I am thrilled to announce that $locationName Location is now officially open and serving our customers! This marks a significant milestone in our company's growth and expansion, and I want to take a moment to thank each and every one of you for your hard work and dedication in making this possible.<br>
                        I am confident that $locationName Location will bring new opportunities and experiences for our customers and employees, and I am proud to have such a talented and committed team in place to make this branch a success.<br>
                        To ensure a smooth transition, please find below some important information that will be helpful:<br>
                        <b>Team members:</b> A detailed list of the team members assigned to $locationName Location will be sent to you shortly.<br>           
                        <b>Branch operations:</b> The branch operations manual and training materials are available for reference and will be sent to you shortly.<br>
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for $locationName Location.<br>
                        <b>Customer support:</b> Our customer support team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to them for any support. <br>
                        <b>Your Login Credentials are:</b><br>                      
                        <b>Url: </b>" . BRANCH_URL . "<br>
                        <b>User Name: </b>" . $userName . "<br>
                        <b>Password: </b>" . $adminPassword . "<br>   
                        Let's work together to make $locationName Location a success. If there is anything else we can do to help, please do not hesitate to contact us.           
                        <br>    
                        Best regards,  $companyNameNav";

                $emailReturn = SendMailByMySMTPmailTemplate($adminEmail, $sub, $msg, $tmpId = null);

                $returnData = $getRes;
            } else {
                $returnData = $getRes;
                $returnData['insRoles'] = $insRoles;
                $returnData['insUser'] = $insUser;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "User already exist";
            // $returnData['sql'] = $sql;
            // $returnData['res'] = $res;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}


function editNewLocationAdministratorUser($POST = [])
{
    global $dbCon;
    global $company_id;
    global $companyNameNav;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:2"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:2 character)"
    ]);

    if ($isValidate["status"] == "success") {

        //locAdminId
        $locAdminId=$POST['locAdminId'];
        $roleAdminId=$POST['roleAdminId'];
        $isLocSuperAdm=$POST['supAdmin'];

        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $fldRoleAccesses='';
        if(isset($POST["fldRoleAccesses"])){

            $fldRoleAccesses = $POST["fldRoleAccesses"];
        }else{
            $fldRoleAccesses = $POST["fldRoleAccessesName"];

        }
        
        $userName = $POST["userName"];
        $adminRole = $POST["adminRole"];

        $bandLocatinId='';
        if(isset($POST["fldAdminBranchLocationId"])){
            $bandLocatinId = explode('|', $POST["fldAdminBranchLocationId"]);

        }else{
            $bandLocatinId = explode('|', $POST["fldAdminBranchLocationIdName"]);
        }
       
        $location_id = $bandLocatinId[0];
        $branch_id = $bandLocatinId[1];
        $locationName = $bandLocatinId[2];
        $adminDesignation = '';
        $adminAvatar = '';
        if (isset($POST["adminAvatar"])) {
            $adminAvatar = uploadFile($POST["adminAvatar"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png"])['data'];
        }

        $roleName = "Custom Location User " . $userName;
        $roleDescription = "Custom Location User For" . $adminName . '(' . $userName . ')';
        // $fldRoleAccesses = 'Location';
        $grandParent = array();
        $subParent = array();
        $subChild = array();
        $subChildMin = array();
        foreach ($_POST["menuFiles"] as $menugrandParentKey => $menugrandParent) {
            $grandParent[] = $menugrandParentKey;
            foreach ($menugrandParent as $menuParentKey => $menuParent) {
                $subParent[] = $menuParentKey;
                foreach ($menuParent as $menuChildKey => $menuChild) {
                    $subChild[] = $menuChildKey;
                    $subChildMin[$menuChildKey] = $menuChild;
                }
            }
        }
        $grandParent = implode(',', $grandParent);
        $subParent = implode(',', $subParent);
        $subChild = implode(',', $subChild);
        $subChildMin = serialize($subChildMin);


        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminUserName`='" . $userName . "'";
        $res = queryGet($sql);
        if ($res['status'] != 'success' || $res['numRows'] ==1) {

            if($isLocSuperAdm!=1){

            $insRoles = "UPDATE  `tbl_branch_admin_roles_a2` SET
                            `fldRoleName`='" . $roleName . "',
                            `fldRoleAccesses`='" . $fldRoleAccesses . "',
                            `grandParent`='" . $grandParent . "',
                            `subParent`='" . $subParent . "',
                            `subChild`='" . $subChild . "',
                            `subChildMin`='" . $subChildMin . "',
                            `fldRoleDescription`='" . $roleDescription . "',
                            `fldRoleCompanyId`='" . $company_id . "',
                            `fldAdminBranchId`='" . $branch_id . "',
                            `fldAdminLocationId`='$location_id'
                            WHERE fldRoleKey=$roleAdminId
                            ";
            $getResponce = queryUpdate($insRoles);
            }
            // $lastId = $getResponce['insertedId'];
            $insUser = "UPDATE  `tbl_branch_admin_details` 
                                SET 
                                    `fldAdminCompanyId`='$company_id',
                                    `fldAdminBranchId`='$branch_id',
                                    `fldAdminBranchLocationId`='$location_id',
                                    `user_type`='$fldRoleAccesses',
                                    `fldAdminName`='$adminName',
                                    `fldAdminEmail`='$adminEmail',
                                    `fldAdminPhone`='$adminPhone',
                                    `fldAdminPassword`='$adminPassword',
                                    `fldAdminUserName` = '$userName',
                                    `flAdminDesignation` = '$adminDesignation',
                                    `fldAdminAvatar` = '$adminAvatar',
                                    `fldAdminRole`=$roleAdminId
                                    WHERE fldAdminKey=$locAdminId
                                    ";
            $getRes = queryUpdate($insUser);
            if ($getRes['status'] == 'success') {
                $sub = "Congratulations on the Successful Launch of $locationName Location!";
                $msg = "Dear " . $adminName . ",<br>           
                        I am thrilled to announce that $locationName Location is now officially open and serving our customers! This marks a significant milestone in our company's growth and expansion, and I want to take a moment to thank each and every one of you for your hard work and dedication in making this possible.<br>
                        I am confident that $locationName Location will bring new opportunities and experiences for our customers and employees, and I am proud to have such a talented and committed team in place to make this branch a success.<br>
                        To ensure a smooth transition, please find below some important information that will be helpful:<br>
                        <b>Team members:</b> A detailed list of the team members assigned to $locationName Location will be sent to you shortly.<br>           
                        <b>Branch operations:</b> The branch operations manual and training materials are available for reference and will be sent to you shortly.<br>
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for $locationName Location.<br>
                        <b>Customer support:</b> Our customer support team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to them for any support. <br>
                        <b>Your Login Credentials are:</b><br>                      
                        <b>Url: </b>" . BRANCH_URL . "<br>
                        <b>User Name: </b>" . $userName . "<br>
                        <b>Password: </b>" . $adminPassword . "<br>   
                        Let's work together to make $locationName Location a success. If there is anything else we can do to help, please do not hesitate to contact us.           
                        <br>    
                        Best regards,  $companyNameNav";

                $emailReturn = SendMailByMySMTPmailTemplate($adminEmail, $sub, $msg, $tmpId = null);

                $returnData = $getRes;
            } else {
                $returnData = $getRes;
                $returnData['insRoles'] = $insRoles;
                $returnData['insUser'] = $insUser;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "User already exist";
            // $returnData['sql'] = $sql;
            // $returnData['res'] = $res;
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
        $fldAdminCompanyId = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];

        $adminAvatar = uploadFile($POST["adminAvatar"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png"]);

        $sql = "SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminEmail`='" . $adminEmail . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_company_admin_details` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "',
                                `fldAdminCompanyId`='" . $fldAdminCompanyId . "',
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
        $sql = "SELECT `fldMenuLabel` FROM `tbl_company_admin_menu` WHERE `fldMenuKey` IN (" . $roleAccesses . ") AND `fldMenuStatus`='active'";
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
