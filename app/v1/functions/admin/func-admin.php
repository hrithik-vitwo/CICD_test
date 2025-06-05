<?php


//START=> Change Status Function 
function adminFuncChangeStatus($data = [], $tableName = "", $tableKeyField = "", $tableStatusField = "status")
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
//END=> Change Status Function 

//====================

//START=> Common Content 
function addNewCommonContent($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "contentName" => "required",
        "contentData" => "required"
    ], [
        "contentName" => "Please enter content name",
        "contentData" => "Please enter content data"
    ]);

    if ($isValidate["status"] == "success") {

        $contentName = $POST["contentName"];
        $contentData = $POST["contentData"];

        $sql = "SELECT * FROM `tbl_common_content` WHERE `contentName`='" . $contentName . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `tbl_common_content` 
                            SET
                                `contentName`='" . $contentName . "',
                                `contentData`='" . $contentData . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Common content added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Common content added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Common content name already exist";
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
function updateCommonContent($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "contentKey" => "required",
        "contentName" => "required",
        "contentData" => "required"
    ], [
        "contentKey" => "Invalid content key",
        "contentName" => "Please enter content name",
        "contentData" => "Please enter content data"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $contentKey = $POST["contentKey"];
    $contentName = $POST["contentName"];
    $contentData = $POST["contentData"];

    $sql = "SELECT * FROM `tbl_common_content` WHERE `contentKey`='" . $contentKey . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) == 1) {
            $sql2 = "SELECT * FROM `tbl_common_content` WHERE `contentName`='" . $contentName . "' AND `contentKey`!='" . $contentKey . "'";
            if ($res2 = mysqli_query($dbCon, $sql2)) {
                if (mysqli_num_rows($res2) == 0) {
                    $updateSQL = "UPDATE `tbl_common_content` 
                            SET
                                `contentName`='" . $contentName . "',
                                `contentData`='" . $contentData . "' WHERE `contentKey`='" . $contentKey . "'";

                    if (mysqli_query($dbCon, $updateSQL)) {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Common content modified success";
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Common content modified failed!";
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Common content name already exists!";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Common content modified failed, try again!";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid common content key!";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
    }
    return $returnData;
}
function getCommonContents($key = null)
{
    global $dbCon;
    $returnData = [];
    if ($key > 0) {
        $sql = "SELECT * FROM `tbl_common_content` WHERE `contentKey`='" . $key . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {

                $returnData['status'] = "success";
                $returnData['message'] = "Common content fetched success";
                $returnData['data'] = mysqli_fetch_assoc($res);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Common content not found";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $sql = "SELECT * FROM `tbl_common_content` WHERE `contentStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Common content fetched success";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Common content not found";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    }
    return $returnData;
}
function getCommonContentData($contentName = null)
{
    global $dbCon;
    if ($contentName != null) {
        $sql = "SELECT * FROM `tbl_common_content` WHERE `contentName`='" . $contentName . "' AND `contentStatus`='active'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {
                return mysqli_fetch_assoc($res)["contentData"];
            }
        }
    }
    return "";
}
//END=> Common Content 

//====================

//START=> Contact Us 
function addNewContactUs($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "contactName" => "required|name",
        "contactEmail" => "required|email",
        "contactSubject" => "required|name",
        "contactText" => "required|text"
    ], [
        "contactName" => "Contact name required",
        "contactEmail" => "Contact email required",
        "contactSubject" => "Contact Subject required",
        "contactText" => "Contact text required"
    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $contactName = $POST["contactName"];
    $contactEmail = $POST["contactEmail"];
    $contactSubject = $POST["contactSubject"];
    $contactText = $POST["contactText"];
    
    $sql = "INSERT INTO `tbl_contact_us` SET `contactName`='".$contactName."',`contactEmail`='".$contactEmail."',`contactSubject`='".$contactSubject."',`contactText`='".$contactText."'";
    if ($res = mysqli_query($dbCon, $sql)) {
        $returnData['status'] = "warning";
        $returnData['message'] = "Thanks for contacting with us, We will get back soon!";
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong, try again!";
    }
    return $returnData;
}

function getContactUsDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    if ($key > 0) {
        $sql = "SELECT * FROM `tbl_contact_us` WHERE `contactKey`='" . $key . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 1) {
                $returnData['status'] = "success";
                $returnData['message'] = "Contact us details fetched success";
                $returnData['data'] = mysqli_fetch_assoc($res);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Contact us details not found";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $sql = "SELECT * FROM `tbl_contact_us` WHERE `contactStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Contact us details fetched success";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Contact us details not found";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    }
    return $returnData;
}
//END=> Contact Us 

//====================

