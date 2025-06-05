<?php
//*************************************/INSERT/******************************************//
function createDataBranches($POST = [])
{
    global $dbCon;
    global $company_id;
    global $companyNameNav;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)"
    ]);

    if ($isValidate["status"] == "success") {
        $admin = array();
        $admin["adminName"] = $POST["adminName"];
        $admin["adminEmail"] = $POST["adminEmail"];
        $admin["adminPhone"] = $POST["adminPhone"];
        $admin["adminPassword"] = $POST["adminPassword"];
        $admin["tablename"] = 'tbl_branch_admin_details';

        if ($POST["createdata"] == 'add_post') {
            $branch_status = 'active';
        } else {
            $branch_status = 'draft';
        }

        $branch_name = $POST["state"];
        $branch_gstin = $POST["branch_gstin"];
        $con_business = $POST["con_business"];
        $region = $POST["region"] ?? '';
        $build_no = $POST["build_no"];
        $flat_no = $POST["flat_no"];
        $street_name = $POST["street_name"];
        $pincode = $POST["pincode"];
        $location = $POST["location"];
        $city = $POST["city"];
        $district = $POST["district"];
        $state = $POST["state"];
        $legal_name = $POST['legal_name'];
        $country =$POST['country'];

        //$branch_code = getRandCodeNotInTable(ERP_BRANCHES,'branch_code');
        // ***************
        $sql = "SELECT branch_code FROM `" . ERP_BRANCHES . "` WHERE company_id=$company_id  ORDER BY branch_id DESC LIMIT 1";
        $lastSoNo = queryGet($sql);
        // console($lastSoNo);
        if (isset($lastSoNo['data'])) {
            $lastSoNo = $lastSoNo['data']['branch_code'] ?? 0;
        } else {
            $lastSoNo = '';
        }
        $branch_code = getBranchSerialNumber($lastSoNo);
        // ***************

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `" . $admin["tablename"] . "` WHERE `fldAdminEmail`='" . $admin["adminEmail"] . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `" . ERP_BRANCHES . "` 
                            SET
                                `branch_name`='" . $branch_name . "',
                                `company_id`='" . $company_id . "',
                                `branch_code`='" . $branch_code . "',
                                `branch_legal_name`='".$legal_name."',
                                `branch_gstin`='" . $branch_gstin . "',
                                `con_business`='" . $con_business . "',
                                `region`='" . $region . "',
                                `build_no`='" . $build_no . "',
                                `flat_no`='" . $flat_no . "',
                                `street_name`='" . $street_name . "',
                                `pincode`='" . $pincode . "',
                                `location`='" . $location . "',
                                `city`='" . $city . "',
                                `district`='" . $district . "',
                                `state`='" . $state . "',
                                `branch_status`='" . $branch_status . "',
                                `country`='" . $country . "'";

                               
                if (mysqli_query($dbCon, $ins)) {
                    $last_id = mysqli_insert_id($dbCon);
                    $admin["fldAdminCompanyId"] = $company_id;
                    $admin["fldAdminBranchId"] = $last_id;
                    // addNewAdministratorUserGlobal($admin);
                    $insad = "INSERT INTO `tbl_branch_admin_details` 
                                SET 
                                  `fldAdminCompanyId`='$company_id',
                                  `fldAdminBranchId`='$last_id',
                                  `fldAdminName`='".$admin["adminName"]."',
                                  `fldAdminEmail`='".$admin["adminEmail"]."',
                                  `fldAdminPhone`='".$admin["adminPhone"]."',
                                  `fldAdminPassword`='".$admin['adminPassword']."',
                                  `fldAdminRole`='1'";
                    //exit();
                    if ($dbCon->query($insad)) {

                        $sub = "Congratulations on the Successful Launch of $branch_name Branch!";
                        $msg = "Dear " . $admin['adminName'] . ",<br>			
                        I am thrilled to announce that $branch_name Branch is now officially open and serving our customers! This marks a significant milestone in our company's growth and expansion, and I want to take a moment to thank each and every one of you for your hard work and dedication in making this possible.<br>
                        I am confident that $branch_name Branch will bring new opportunities and experiences for our customers and employees, and I am proud to have such a talented and committed team in place to make this branch a success.<br>
                        To ensure a smooth transition, please find below some important information that will be helpful:<br>
                        <b>Team members:</b> A detailed list of the team members assigned to $branch_name Branch will be sent to you shortly.<br>			
                        <b>Branch operations:</b> The branch operations manual and training materials are available for reference and will be sent to you shortly.<br>
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for $branch_name Branch.<br>
                        <b>Customer support:</b> Our customer support team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to them for any support.	<br>
                        <b>Your Login Credentials are:</b><br>            			
                        <b>Url: </b>" . BRANCH_URL . "<br>
                        <b>User Name: </b>" . $POST["adminEmail"] . "<br>
                        <b>Password: </b>" . $POST["adminPassword"] . "<br>   
                        Let's work together to make $branch_name branch a success. If there is anything else we can do to help, please do not hesitate to contact us.			
                        <br> 	
                        Best regards,  $companyNameNav";
                        SendMailByMySMTPmailTemplate($POST["adminEmail"], $sub, $msg);

                        global $current_userName;
                        global $companyNameNav;

                        $whatsapparray = [];
                        $whatsapparray['templatename'] = 'after_creating_a_branch';
                        $whatsapparray['to'] = $admin["adminPhone"];
                        $whatsapparray['companyname_branch'] = $companyNameNav . ' (' . $branch_name . ')';
                        $whatsapparray['username'] = $admin["adminEmail"];
                        $whatsapparray['password'] = $admin["adminPassword"];
                        $whatsapparray['quickcontact'] = null;
                        $whatsapparray['current_userName'] = $current_userName;

                        SendMessageByWhatsappTemplate($whatsapparray);

                        $returnData['status'] = "success";
                        $returnData['message'] = "Branch added success, Login credentials sent on your email address.";
                        $returnData['branchId'] = $last_id;
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Branch user added failed";
                        $returnData['insad'] = $insad;
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Branch added failed: 2";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "POC Email already exist";
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

//*************************************/UPDATE/******************************************//
// function updateDataBranches($POST)
// {
//     global $dbCon;
//     $returnData = [];
//     $isValidate = validate($POST, [
//         "adminKey" => "required",
//         "adminName" => "required",
//         "adminEmail" => "required|email",
//         "adminPhone" => "required|min:10|max:10",
//         "adminPassword" => "required|min:8",
//         "adminRole" => "required",
//     ], [
//         "adminKey" => "Invalid admin",
//         "adminName" => "Enter name",
//         "adminEmail" => "Enter valid email",
//         "adminPhone" => "Enter valid phone",
//         "adminPassword" => "Enter password(min:8 character)",
//         "adminRole" => "Select a role",
//     ]);

//     if ($isValidate["status"] == "success") {

//         $adminKey = $POST["adminKey"];
//         $adminName = $POST["adminName"];
//         $adminEmail = $POST["adminEmail"];
//         $adminPhone = $POST["adminPhone"];
//         $adminPassword = $POST["adminPassword"];
//         $adminRole = $POST["adminRole"];

//         $sql = "SELECT * FROM `".ERP_BRANCHES."` WHERE `fldAdminKey`='" . $adminKey . "'";
//         if ($res = mysqli_query($dbCon, $sql)) {
//             if (mysqli_num_rows($res) > 0) {
//                 $ins = "UPDATE `".ERP_BRANCHES."` 
//                             SET
//                                 `fldAdminName`='" . $adminName . "',
//                                 `fldAdminEmail`='" . $adminEmail . "',
//                                 `fldAdminPhone`='" . $adminPhone . "',
//                                 `fldAdminPassword`='" . $adminPassword . "',
//                                 `fldAdminRole`='" . $adminRole . "' WHERE `fldAdminKey`='" . $adminKey . "'";

//                 if (mysqli_query($dbCon, $ins)) {
//                     $returnData['status'] = "success";
//                     $returnData['message'] = "Admin modified success";
//                 } else {
//                     $returnData['status'] = "warning";
//                     $returnData['message'] = "Admin modified failed";
//                 }
//             } else {
//                 $returnData['status'] = "warning";
//                 $returnData['message'] = "Admin not exist";
//             }
//         } else {
//             $returnData['status'] = "warning";
//             $returnData['message'] = "Somthing went wrong";
//         }
//     } else {
//         $returnData['status'] = "warning";
//         $returnData['message'] = "Invalid form inputes";
//         $returnData['errors'] = $isValidate["errors"];
//     }
//     return $returnData;
// }

function updateDataBranches($POST)
{
    // console($POST);
    // exit();
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)"
    ]);

    if ($isValidate["status"] == "success") {
        $admin = array();
        $admin["adminName"] = $POST["adminName"];
        $admin["adminEmail"] = $POST["adminEmail"];
        $admin["adminPhone"] = $POST["adminPhone"];
        $admin["adminPassword"] = $POST["adminPassword"];
        $admin["tablename"] = 'tbl_branch_admin_details';
        $admin["adminPassword"] = $POST["adminPassword"];



        $branch_name = $POST["branch_name"];
        $branch_gstin = $POST["branch_gstin"];
        $con_business = $POST["con_business"];
        $region = $POST["region"] ?? '';
        $build_no = $POST["build_no"];
        $flat_no = $POST["flat_no"];
        $street_name = $POST["street_name"];
        $pincode = $POST["pincode"];
        $location = $POST["location"];
        $city = $POST["city"];
        $district = $POST["district"];
        $state = $POST["state"];
        $company_id = $POST["fldAdminCompanyId"];

        $id = $POST['branch_id'];
        $admin_id = $_POST['admin_id'];

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);



        $ins = "UPDATE `" . ERP_BRANCHES . "` 
                            SET
                                `branch_name`='" . $branch_name . "',
                                `company_id`='" . $company_id . "',
                                `branch_gstin`='" . $branch_gstin . "',
                                `con_business`='" . $con_business . "',
                                `region`='" . $region . "',
                                `build_no`='" . $build_no . "',
                                `flat_no`='" . $flat_no . "',
                                `street_name`='" . $street_name . "',
                                `pincode`='" . $pincode . "', 
                                `location`='" . $location . "',
                                `city`='" . $city . "',
                                `district`='" . $district . "',
                                `state`='" . $state . "' WHERE `branch_id`=$id
                               ";

        $returnData = queryUpdate($ins);
        if ($returnData['status'] == "success") {
            $admin_update = "UPDATE `tbl_branch_admin_details`
                SET
                    `fldAdminName`='" . $admin["adminName"] . "',
                    `fldAdminEmail`='" . $admin["adminEmail"] . "',
                    `fldAdminPassword`='" . $admin["adminPassword"] . "',
                    `fldAdminPhone`='" . $admin["adminPhone"] . "' WHERE `fldAdminKey`=$admin_id ";
            $returnData = queryUpdate($admin_update);
        } else {
            return $returnData;
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllDataBranches($fldAdminCompanyId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_BRANCHES . "` WHERE `status`!='deleted' AND fldAdminCompanyId='" . $fldAdminCompanyId . "'";
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

//*************************************/SELECT SINGLE/******************************************//
function getDataDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_BRANCHES . "` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
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
//*************************************************Visit Branches*********************************************** */

function VisitBranches($POST)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminCompanyId`='" . $POST["fldAdminCompanyId"] . "' AND `fldAdminBranchId`='" . $POST["fldAdminBranchId"] . "' AND `fldAdminStatus`='active' ORDER BY fldAdminKey ASC limit 1";
    if ($result = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["visitCompanyAdminInfo"] = $_SESSION["logedCompanyAdminInfo"];
            unset($_SESSION["logedCompanyAdminInfo"]);

            $_SESSION["logedBranchAdminInfo"]["adminId"] = $row["fldAdminKey"];
            $_SESSION["logedBranchAdminInfo"]["adminName"] = $row["fldAdminName"];
            $_SESSION["logedBranchAdminInfo"]["adminEmail"] = $row["fldAdminEmail"];
            $_SESSION["logedBranchAdminInfo"]["adminPhone"] = $row["fldAdminPhone"];
            $_SESSION["logedBranchAdminInfo"]["adminRole"] = $row["fldAdminRole"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] = $row["fldAdminCompanyId"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] = $row["fldAdminBranchId"];
            $_SESSION["logedBranchAdminInfo"]["adminType"] = 'branch';
            $returnData["status"] = "success";
            $returnData["message"] = "Login success";
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

//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusBranches($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $tableName = ERP_BRANCHES;
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

//*************************************/END/******************************************//