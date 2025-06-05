<?php

function administratorAuth()
{
    global $dbCon;
    $commonAccessPages = ["index.php", "empty-page.php", "empty-form.php"];
    if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
        redirect(BRANCH_URL . "login.php");
    } else {
        $adminRole = $_SESSION["logedBranchAdminInfo"]["adminRole"];
        $adminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($adminType == "location") {
            $url = LOCATION_URL;
        } else {
            $url = BRANCH_URL;
        }
        if ($adminType == "branch") {
            if ($adminRole == 1) {
                if (!in_array($currentPage, $commonAccessPages)) {
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE  `menuFor` ='" . $adminType . "' AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            } else if ($adminRole == 2) {
                if (!in_array($currentPage, $commonAccessPages)) {
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE  `menuFor` ='" . $adminType . "' AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            } else {
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
                        redirect($url);
                    }
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            }
        } else {
            redirect($url);
        }
    }
}

function administratorLocationAuth()
{
    global $dbCon;
    $commonAccessPages = ["index.php", "empty-page.php", "empty-form.php"];
    if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
        redirect(BRANCH_URL . "login.php");
    } else {
        $adminRole = $_SESSION["logedBranchAdminInfo"]["adminRole"];
        $adminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($adminType == "location") {
            $url = LOCATION_URL;
        } else {
            $url = BRANCH_URL;
        }
        if ($adminType == "location") {
            if ($adminRole == 1) {
                if (!in_array($currentPage, $commonAccessPages)) {
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE  `menuFor` ='" . $adminType . "' AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            } else if ($adminRole == 2) {
                if (!in_array($currentPage, $commonAccessPages)) {
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE  `menuFor` ='" . $adminType . "' AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            } else {
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
                        redirect($url);
                    }
                    $sqlCheckAccessableFile = "SELECT `fldMenuFile` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldMenuFile`='" . $currentPage . "'";

                    if ($qryCheckAccessableFile = mysqli_query($dbCon, $sqlCheckAccessableFile)) {
                        if (mysqli_num_rows($qryCheckAccessableFile) < 1) {
                            redirect($url);
                        }
                    } else {
                        redirect($url);
                    }
                }
            }
        } else {
            redirect($url);
        }
    }
}


function getAdministratorMenuSubMenu()
{
    global $dbCon;
    $returnData = [];
    $accesses = "";
    $adminRole = $_SESSION["logedBranchAdminInfo"]["adminRole"];
    $adminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
    if ($adminRole == 1 && $adminType == 'branch') {
        $sqlAccesses = "SELECT `fldMenuKey` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldParentMenuKey`!='-0' AND `menuFor`='branch' AND `fldMenuStatus`='active'";
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accessesArr = mysqli_fetch_all($qryAccesses, MYSQLI_ASSOC);
            $accesses = implode(",", array_column($accessesArr, 'fldMenuKey'));
        }
    } else if ($adminRole == 2 && $adminType == 'location') {
        $sqlAccesses = "SELECT `fldMenuKey` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldParentMenuKey`!='-0' AND `menuFor`='location' AND `fldMenuStatus`='active'";
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accessesArr = mysqli_fetch_all($qryAccesses, MYSQLI_ASSOC);
            $accesses = implode(",", array_column($accessesArr, 'fldMenuKey'));
        }
    } else {
        $sqlAccesses = "SELECT `subChild` FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleStatus`='active' AND `fldRoleKey`=" . $adminRole;
        if ($qryAccesses = mysqli_query($dbCon, $sqlAccesses)) {
            $accesses = mysqli_fetch_assoc($qryAccesses)["subChild"];
        }
    }

    if ($accesses == "") {
        $returnData["status"] = "warning";
        $returnData["message"] = "Menu not found";
        return $returnData;
        exit();
    }


    $grandParentMenuKeysArr = [];
    $sqlgrandParentMenuKeys = "SELECT `fldGrandParentMenu` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' GROUP BY `fldGrandParentMenu`;";
    if ($qrygrandParentMenuKeys = mysqli_query($dbCon, $sqlgrandParentMenuKeys)) {
        if (mysqli_num_rows($qrygrandParentMenuKeys) > 0) {
            while ($row = mysqli_fetch_assoc($qrygrandParentMenuKeys)) {
                $grandParentMenuKeysArr[] = $row["fldGrandParentMenu"];
            }
        }
    }


    $grandParentMenuKeys = implode(",", $grandParentMenuKeysArr);

    $parentMenuKeysArr = [];
    $sqlParentMenuKeys = "SELECT `fldParentMenuKey` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' GROUP BY `fldParentMenuKey`;";
    if ($qryParentMenuKeys = mysqli_query($dbCon, $sqlParentMenuKeys)) {
        if (mysqli_num_rows($qryParentMenuKeys) > 0) {
            while ($row = mysqli_fetch_assoc($qryParentMenuKeys)) {
                $parentMenuKeysArr[] = $row["fldParentMenuKey"];
            }
        }
    }

    $parentMenuKeys = implode(",", $parentMenuKeysArr);

    $sqlGrandMenuList = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $grandParentMenuKeys . ") AND `fldMenuStatus`='active' ORDER BY `fldMenuOrderBy` ASC";
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
                $returnData["data"][$grandParentMenuKey]["redirectInUrl"] = $rowGrandMenuList["redirectInUrl"];
                $returnData["data"][$grandParentMenuKey]["subParentMenus"] = [];

                $sqlMenuList = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $parentMenuKeys . ") AND `fldMenuStatus`='active' ORDER BY `fldMenuOrderBy` ASC";
                if ($qryMenuList = mysqli_query($dbCon, $sqlMenuList)) {
                    if (mysqli_num_rows($qryMenuList) > 0) {
                        $menuLoop = -1;
                        while ($rowMenuList = mysqli_fetch_assoc($qryMenuList)) {
                            $menuLoop++;
                            $parentMenuKey = $rowMenuList["fldMenuKey"];
                            $sqlSubMenuList = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $accesses . ") AND `fldMenuStatus`='active' AND `fldParentMenuKey`=" . $parentMenuKey . " AND `fldGrandParentMenu`=" . $grandParentMenuKey . " ORDER BY `fldMenuOrderBy` ASC";
                            if ($qrySubMenuList = mysqli_query($dbCon, $sqlSubMenuList)) {
                                if (mysqli_num_rows($qrySubMenuList) > 0) {
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuLabel"] = $rowMenuList["fldMenuLabel"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuIcon"] = $rowMenuList["fldMenuIcon"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["extraPrefixFolder"] = $rowMenuList["extraPrefixFolder"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["menuFile"] = $rowMenuList["fldMenuFile"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["sidebar_view"] = $rowMenuList["sidebar_view"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["visibility"] = $rowMenuList["visibility"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["redirectInUrl"] = $rowMenuList["redirectInUrl"];
                                    $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"] = [];

                                    $subMenuLoop = -1;
                                    while ($rowSubMenuList = mysqli_fetch_assoc($qrySubMenuList)) {
                                        $subMenuLoop++;
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuLabel"] = $rowSubMenuList["fldMenuLabel"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuIcon"] = $rowSubMenuList["fldMenuIcon"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["extraPrefixFolder"] = $rowSubMenuList["extraPrefixFolder"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["menuFile"] = $rowSubMenuList["fldMenuFile"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["sidebar_view"] = $rowSubMenuList["sidebar_view"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["visibility"] = $rowSubMenuList["visibility"];
                                        $returnData["data"][$grandParentMenuKey]["subParentMenus"][$menuLoop]["subMenus"][$subMenuLoop]["redirectInUrl"] = $rowSubMenuList["redirectInUrl"];
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


function noAccess($width)
{ ?>
    <div class="noAccess_security" style="width:<?= $width ?>;"><img src="<?= BASE_URL ?>public/assets/img/security_lock.png" alt="" align="absmiddle" />&nbsp;&nbsp;Access Denied</div>
<?php
}
function checkAccess($BtnName)
{
    global $dbCon;
    $pageName = basename($_SERVER['PHP_SELF']);
    $adminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
    $adminRole = $_SESSION["logedBranchAdminInfo"]["adminRole"];
    if ($adminRole != '1' && $adminRole != '2') {
        $sql = "SELECT `fldMenuKey` FROM `tbl_branch_admin_menu_rule_book` WHERE 1 AND `fldGrandParentMenu`!='' AND `fldParentMenuKey`!='0' AND `fldParentMenuKey`!='-0' AND `fldMenuFile`='" . $pageName . "' AND `menuFor`='" . $adminType . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $mnuKey = $row['fldMenuKey'];
                $access = getAdministratorRoleDetails($adminRole);
                $subChildMin = unserialize($access['data']['subChildMin']);
                if (in_array("$BtnName", $subChildMin[$mnuKey])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function ForgotAdministratorUser($POST)
{
    global $dbCon;
    $returnData = [];
    $adminPassword = rand(0000, 999999);
    $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminEmail`='" . $POST["email"] . "' AND `fldAdminStatus`='active'";
    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $ins = "UPDATE `tbl_branch_admin_details` 
                        SET 
                            `fldAdminPassword`='" . $adminPassword . "'
                         WHERE `fldAdminKey`='" . $row['fldAdminKey'] . "'";

            if (mysqli_query($dbCon, $ins)) {
                $to = $POST["email"];
                $sub = 'Password Forgot successfull';
                $user_name = $row['fldAdminName'];
                $url = BRANCH_URL;
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


function VisitBranchesUserLogout(){
    global $dbCon;
    $returnData=[];
    if(isset($_SESSION["visitCompanyAdminInfo"]) && !empty($_SESSION["visitCompanyAdminInfo"])){        
        unset($_SESSION["logedBranchAdminInfo"]);
        $row=$_SESSION["visitCompanyAdminInfo"];

        $_SESSION["logedCompanyAdminInfo"]["adminId"]=$row["adminId"];
        $_SESSION["logedCompanyAdminInfo"]["adminName"]=$row["adminName"];
        $_SESSION["logedCompanyAdminInfo"]["adminEmail"]=$row["adminEmail"];
        $_SESSION["logedCompanyAdminInfo"]["adminPhone"]=$row["adminPhone"];
        $_SESSION["logedCompanyAdminInfo"]["adminRole"]=$row["adminRole"];
        $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]=$row["fldAdminCompanyId"];
        $_SESSION["logedCompanyAdminInfo"]["adminType"]='branch';
              
        unset($_SESSION["visitCompanyAdminInfo"]);
        $returnData["status"]="success";
        $returnData["message"]="Process success";        
    }else{
        $returnData["status"]="warning";
        $returnData["message"]="Something went wrong, Try again...!";
    }
    return $returnData;
}

function VisitLocationsUserLogout(){
    global $dbCon;
    $returnData=[];
    if(isset($_SESSION["visitBranchAdminInfo"]) && !empty($_SESSION["visitBranchAdminInfo"])){        
        unset($_SESSION["logedBranchAdminInfo"]);      
        unset($_SESSION['menuSubMenuListObj']);
        $row=$_SESSION["visitBranchAdminInfo"];

        $_SESSION["logedBranchAdminInfo"]["adminId"]=$row["adminId"];
        $_SESSION["logedBranchAdminInfo"]["adminName"]=$row["adminName"];
        $_SESSION["logedBranchAdminInfo"]["adminEmail"]=$row["adminEmail"];
        $_SESSION["logedBranchAdminInfo"]["adminPhone"]=$row["adminPhone"];
        $_SESSION["logedBranchAdminInfo"]["adminRole"]=$row["adminRole"];
        $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]=$row["fldAdminCompanyId"];
        $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]=$row["fldAdminBranchId"];
        $_SESSION["logedBranchAdminInfo"]["adminType"]='branch';     
        unset($_SESSION["visitBranchAdminInfo"]);
        $returnData["status"]="success";
        $returnData["message"]="Process success";        
    }else{
        $returnData["status"]="warning";
        $returnData["message"]="Something went wrong, Try again...!";
    }
    return $returnData;
}


function loginAdministratorUser($POST)
{
    global $dbCon;
    $returnData = [];
    $username = $POST["email"];
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminEmail`='" . $POST["email"] . "' AND `fldAdminStatus`='active'";
    } else {
        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminUserName`='" . $POST["email"] . "' AND `fldAdminStatus`='active'";
    }

    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $result = checkStatus($row['fldAdminCompanyId'], $row['fldAdminBranchId'], $row['fldAdminBranchLocationId']);
            if ($result['status'] == "active") {
                if ($POST["pass"] == $row["fldAdminPassword"]) {
                    $_SESSION["logedBranchAdminInfo"]["adminId"] = $row["fldAdminKey"];
                    $_SESSION["logedBranchAdminInfo"]["adminName"] = $row["fldAdminName"];
                    $_SESSION["logedBranchAdminInfo"]["adminEmail"] = $row["fldAdminEmail"];
                    $_SESSION["logedBranchAdminInfo"]["adminPhone"] = $row["fldAdminPhone"];
                    $_SESSION["logedBranchAdminInfo"]["adminRole"] = $row["fldAdminRole"];
                    $_SESSION["logedBranchAdminInfo"]["flAdminVariant"] = $row["flAdminVariant"];
                    $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] = $row["fldAdminCompanyId"];
                    $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] = $row["fldAdminBranchId"];

                    //check_country
                    $get_country = queryGet("SELECT * FROM `erp_companies` WHERE company_id = '" . $row["fldAdminCompanyId"] . "'");

                    $_SESSION["logedBranchAdminInfo"]["companyCountry"] = $get_country['data']['company_country'];


                    if (!empty($row["fldAdminBranchLocationId"])) {
                        $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"] = $row["fldAdminBranchLocationId"];
                        $_SESSION["logedBranchAdminInfo"]["adminType"] = 'location';
                    } else {
                        $_SESSION["logedBranchAdminInfo"]["adminType"] = 'branch';
                    }
                    $returnData["status"] = "success";
                    $returnData["message"] = "Login success";
                } else {
                    $returnData["status"] = "warning";
                    $returnData["message"] = "Invalid Password, Try again...!";
                }
            } else {
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

function checkStatus($company_id, $branch_id, $location_id = null)
{
    if (!$company_id && !$branch_id) {
        return ['status' => 'inactive', 'message' => 'Missing company or branch ID'];
    }

    if (!queryGet("SELECT 1 FROM erp_companies WHERE company_id = '$company_id' AND company_status = 'active' LIMIT 1")['numRows']) {
        return ['status' => 'inactive', 'message' => 'Company inactive'];
    }

    if (!queryGet("SELECT 1 FROM erp_branches WHERE branch_id = '$branch_id' AND company_id = '$company_id' AND branch_status = 'active' LIMIT 1")['numRows']) {
        return ['status' => 'inactive', 'message' => 'Branch inactive'];
    }

    if ($location_id && !queryGet("SELECT 1 FROM erp_branch_otherslocation WHERE othersLocation_id = '$location_id' AND company_id = '$company_id' AND branch_id = '$branch_id' AND othersLocation_status = 'active' LIMIT 1")['numRows']) {
        return ['status' => 'inactive', 'message' => 'Location inactive'];
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

    $logoObj = uploadFile($POST["logo"], "../public/storage/logo/", ["jpg", "png", "ico", "jpeg"]);
    $faviconObj = uploadFile($POST["favicon"], "../public/storage/logo/", ["jpg", "png", "ico", "jpeg"]);
    $prevLogo = $prevFavicon = "";
    if ($logoObj["status"] == "success") {
        $prevLogo = getAdministratorSettings("logo");
        if ($prevLogo != "") {
            $prevLogo = "../public/storage/logo/" . $prevLogo;
        }

        array_push($sql, "UPDATE `tbl_admin_settings` SET `fldSettingValue`='" . $logoObj["data"] . "' WHERE `fldSettingName`='logo'");
    }
    if ($faviconObj["status"] == "success") {
        $prevFavicon = getAdministratorSettings("favicon");
        if ($prevFavicon != "") {
            $prevFavicon = "../public/storage/logo/" . $prevFavicon;
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
    $fldGrandParentMenu = $POST['fldGrandParentMenu'];
    $menuLabel = $POST['menuLabel'];
    $menuFile = $POST['menuFile'];
    $menuIcon = $POST['menuIcon'];
    $menuOrderBy = $POST['menuOrderBy'];

    $ins = "INSERT INTO `tbl_branch_admin_menu_rule_book`
                SET
                    `fldGrandParentMenu` = '" . $fldGrandParentMenu . "',
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
    $fldGrandParentMenu = $POST["fldGrandParentMenu"];
    $menuLabel = $POST['menuLabel'];
    $menuFile = $POST['menuFile'];
    $menuIcon = $POST['menuIcon'];
    $menuOrderBy = $POST['menuOrderBy'];
    $menuStatus = $POST['menuStatus'];

    if ($res = mysqli_query($dbCon, "SELECT COUNT(`fldMenuLabel`) AS 'noOfRecords' FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey`=" . $POST["menuKey"])) {
        if (mysqli_fetch_assoc($res)["noOfRecords"] == 1) {
            $ins = "UPDATE `tbl_branch_admin_menu_rule_book`
                SET
                    `fldMenuLabel` = '" . $menuLabel . "',
                    `fldMenuIcon` = '" . $menuIcon . "',
                    `fldMenuFile` = '" . $menuFile . "',
                    `fldMenuOrderBy` = '" . $menuOrderBy . "',
                    `fldGrandParentMenu` = '" . $fldGrandParentMenu . "',
                    `fldMenuStatus` = '" . $menuStatus . "'
                WHERE 
                    `fldMenuKey`=" . $menuKey;
            if (mysqli_query($dbCon, $ins)) {
                $ins2 = "UPDATE `tbl_branch_admin_menu_rule_book`
                    SET
                        `fldGrandParentMenu` = '" . $fldGrandParentMenu . "'
                    WHERE 
                        `fldParentMenuKey`=" . $menuKey;
                mysqli_query($dbCon, $ins2);

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

function updateAdministratorSubMenu($POST)
{
    global $dbCon;
    $menuKey = $POST["menuKey"];
    $parentMenuKey = $POST["parentMenuKey"];
    $return = getAdministratorMenuDetails($parentMenuKey);
    $fldGrandParentMenu = $return['data']["fldGrandParentMenu"] ?? '';
    $menuLabel = $POST['menuLabel'];
    $menuFile = $POST['menuFile'];
    $menuIcon = $POST['menuIcon'];
    $menuOrderBy = $POST['menuOrderBy'];
    $menuStatus = $POST['menuStatus'];
    $accessaArrs = preg_replace("![^a-z0-9]+!i", "", $POST["access"]);
    $accessaArr = implode(',', $accessaArrs);

    if ($res = mysqli_query($dbCon, "SELECT COUNT(`fldMenuLabel`) AS 'noOfRecords' FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey`=" . $POST["menuKey"])) {
        if (mysqli_fetch_assoc($res)["noOfRecords"] == 1) {
            $ins = "UPDATE `tbl_branch_admin_menu_rule_book`
                SET
                    `fldMenuLabel` = '" . $menuLabel . "',
                    `fldMenuIcon` = '" . $menuIcon . "',
                    `fldMenuFile` = '" . $menuFile . "',
                    `fldMenuOrderBy` = '" . $menuOrderBy . "',
                    `fldGrandParentMenu` = '" . $fldGrandParentMenu . "',
                    `fldParentMenuKey` = '" . $parentMenuKey . "',
                    `menu_accesses` = '" . $accessaArr . "',
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
function addAdministratorSubMenuAccess($accessaArr, $parentMenuKey, $menuFile)
{
    global $dbCon;
    foreach ($accessaArr as $key => $value) {
        if (!empty($value[0])) {
            $inss = "UPDATE `tbl_branch_admin_menu_rule_book_access`
                SET
                    `fldMenuLabel` = '" . $value[1] . "',
                    `fldMenuFile` = '" . $menuFile . "',
                    `fldParentMenuKey` = '" . $parentMenuKey . "'
                WHERE 
                    `fldMenuKey`=" . $value[0];
        } else {
            $inss = "INSERT INTO `tbl_branch_admin_menu_rule_book_access`
                    SET
                        `fldMenuLabel` = '" . $value[1] . "',
                        `fldMenuFile` = '" . $menuFile . "',
                        `fldParentMenuKey` = '" . $parentMenuKey . "'";
        }
        mysqli_query($dbCon, $inss);
    }
}


function addAdministratorSubMenu($POST)
{
    global $dbCon;
    $menuKey = $POST['menuKey'];
    $return = getAdministratorMenuDetails($menuKey);
    $fldGrandParentMenu = $return['data']["fldGrandParentMenu"] ?? '';
    $subMenuLabel = $POST['subMenuLabel'];
    $subMenuFile = $POST['subMenuFile'];
    $subMenuIcon = $POST['subMenuIcon'];
    $subMenuOrderBy = $POST['subMenuOrderBy'];
    $accessaArrs = preg_replace("![^a-z0-9]+!i", "", $POST["access"]);;
    $accessaArr = implode(',', $accessaArrs);
    $ins = "INSERT INTO `tbl_branch_admin_menu_rule_book` 
                SET
                    `fldMenuLabel` = '" . $subMenuLabel . "',
                    `fldParentMenuKey` = '" . $menuKey . "',
                    `fldGrandParentMenu` = '" . $fldGrandParentMenu . "',
                    `fldMenuIcon` = '" . $subMenuIcon . "',
                    `fldMenuFile` = '" . $subMenuFile . "',
                    `menu_accesses` = '" . $accessaArr . "',
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

function getAdministratorMenuList($parentMenuKey = '0')
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldParentMenuKey`='" . $parentMenuKey . "' AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
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
function getAdministratorMenuListNew($parentMenuKey = '0', $menufor=null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldParentMenuKey`='" . $parentMenuKey . "' AND `menuFor`='" . $menufor . "' AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
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

function getAdministratorGrandMenuList($parentMenuKey, $grandparentMenuKey)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldParentMenuKey`='" . $parentMenuKey . "' AND `fldGrandParentMenu`='" . $grandparentMenuKey . "' AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
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

function getAdministratorSubMenuListAccess($parentMenuKey = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_menu_rule_book_access` WHERE `fldParentMenuKey`=" . $parentMenuKey . " AND `fldMenuStatus`!='deleted' ORDER BY `fldMenuOrderBy` ASC";
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

function getAdministratorMenuDetails($menuKey = '0')
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey`=" . $menuKey;
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

        $fldRoleCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
        $fldAdminBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];


        $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleCompanyId`='" . $fldRoleCompanyId . "' AND `fldAdminBranchId`='" . $fldAdminBranchId . "' AND `fldRoleStatus`!='deleted'";

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
                            `fldRoleCompanyId`='" . $fldRoleCompanyId . "',
                            `fldAdminBranchId`='" . $fldAdminBranchId . "'";

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

        $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleKey`=" . $roleKey . " AND `fldRoleStatus`!='deleted'";

        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {

                $checkName = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleName`='" . $roleName . "' AND `fldRoleKey`!=" . $roleKey . " AND  `fldRoleStatus`!='deleted'";
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
    $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleKey`=" . $key . "";
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
    $fldRoleCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
    $fldAdminBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $sql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE `fldRoleStatus`!='deleted' AND (`fldRoleCompanyId`=" . $fldRoleCompanyId . " OR `fldRoleCompanyId`=0) AND (`fldAdminBranchId`=" . $fldAdminBranchId . " OR `fldAdminBranchId`=0)";
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
    $fldAdminCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
    $fldAdminBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_details`,`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminCompanyId`='" . $fldAdminCompanyId . "' AND `tbl_branch_admin_details`.`fldAdminBranchId`='" . $fldAdminBranchId . "' AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted'";

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

    $sql = "SELECT `tbl_branch_admin_details`.*, `tbl_branch_admin_roles_a2`.`fldRoleName` FROM `tbl_branch_admin_details`,`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted' AND `fldAdminKey`=" . $key;

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

        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `tbl_branch_admin_details` 
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
        "fldAdminBranchLocationId" => "required",
        "adminAvatar" => "required"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:8 character)",
        "adminRole" => "Select a role",
        "fldAdminBranchLocationId" => "Select role for",
        "adminAvatar" => "Select a avatar/photo"
    ]);

    if ($isValidate["status"] == "success") {

        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $adminRole = $POST["adminRole"];
        if($POST["fldAdminBranchLocationId"]==0){
        $fldAdminBranchLocationId = '';
        } else{
        $fldAdminBranchLocationId = $POST["fldAdminBranchLocationId"];            
        }
        
        $fldAdminCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
        $fldAdminBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];

        $adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/", ["jpg", "jpeg", "png"]);

        $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminEmail`='" . $adminEmail . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_branch_admin_details` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "',
                                `fldAdminBranchLocationId`='" . $fldAdminBranchLocationId . "',
                                `fldAdminCompanyId`='" . $fldAdminCompanyId . "',
                                `fldAdminBranchId`='" . $fldAdminBranchId . "',
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
        $sql = "SELECT `fldMenuLabel` FROM `tbl_branch_admin_menu_rule_book` WHERE `fldMenuKey` IN (" . $roleAccesses . ") AND `fldMenuStatus`='active'";
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
