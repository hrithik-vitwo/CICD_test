<?php
class Database
{
    private $PROJECT_MODE = "QA"; // LOCAL / QA / A2

    // ========================== DB CRED ========================= //
    private $DB_HOST = 'one.vitwo.ai';
    private $DB_USER = 'vitwo_one_user';
    private $DB_PASS = 'VitwoOneDb@12345';
    private $DB_NAME = 'vitwo_one';

    private $DB_LOCAL_HOST = '192.168.0.250';
    private $DB_LOCAL_USER = 'localuser';
    private $DB_LOCAL_PASS = 'Local@12345678';
    private $DB_LOCAL_NAME = 'devalpha';

    // private $DB_LOCAL_HOST = 'one.vitwo.ai';
    // private $DB_LOCAL_USER = 'vitwo_one_user';
    // private $DB_LOCAL_PASS = 'VitwoOneDb@12345';
    // private $DB_LOCAL_NAME = 'vitwo_one';

    private $DB_TIMEZONE = '+5:30';
    // ============================================================ //


    private $conn;
    private $isTransEnabled = false;
    private $actionName = "Data";
    private $actionSuccessMsg = "Data saved successfully";
    private $actionErrorMsg = "Data saved failed, please try again";
    private $actionReturnData = [];

    function __construct($isTransEnabled = false)
    {
        $this->isTransEnabled = $isTransEnabled;
        if($this->PROJECT_MODE=="LOCAL"){
            $this->conn = new mysqli($this->DB_LOCAL_HOST, $this->DB_LOCAL_USER, $this->DB_LOCAL_PASS, $this->DB_LOCAL_NAME);
        }else{
            $this->conn = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
        }
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        } else {
            $this->conn->query("SET time_zone='" . $this->DB_TIMEZONE . "'");
            if ($this->isTransEnabled) {
                $this->conn->begin_transaction();
            }
        }
    }
    function setActionName($actionName = "Data")
    {
        $this->actionName = $actionName;
    }
    function setSuccessMsg($successMsg = "Data successfully created")
    {
        $this->actionSuccessMsg = $successMsg;
    }
    function setErrorMsg($errorMsg = "Data creation failed, please try again")
    {
        $this->actionErrorMsg = $errorMsg;
    }

    function saveTheQueryLog($query)
    {
        if (file_exists("/home/devalpha/public_html/log.txt")) {
            $logFilePath = "/home/devalpha/public_html/log.txt";
        } else if (file_exists("/var/www/one.vitwo.ai/public_html/log.txt")) {
            $logFilePath = "/var/www/one.vitwo.ai/public_html/log.txt";
        } else {
            $logFilePath = "";
        }
        if ($logFilePath != "") {
            $userIP = $_SERVER['REMOTE_ADDR'] ?? "0.0.0.0";
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "";
            $timestamp = date("Y-m-d H:i:s");
            $logMessage = "$userIP - $timestamp - $userAgent - SQL: $query\n";
            file_put_contents($logFilePath, $logMessage, FILE_APPEND);
        }
    }

    function queryGet($query, $isMultipleRows = false)
    {
        $returnData = [];
        if ($result = $this->conn->query($query)) {
            $numRows = $result->num_rows;
            if ($numRows > 0) {
                $returnData = [
                    "status" => "success",
                    "message" => ($numRows > 1) ? $numRows . " records found successfully" : $numRows . " record found successfully",
                    "numRows" => $numRows,
                    "query" => $query,
                    "data" => ($isMultipleRows) ? $result->fetch_all(MYSQLI_ASSOC) : $result->fetch_assoc()
                ];
            } else {
                $returnData = [
                    "status" => "warning",
                    "message" => "Record not found",
                    "numRows" => 0,
                    "query" => $query,
                    "data" => []
                ];
            }
        } else {
            $returnData = [
                "status" => "failed",
                "message" => "Something went wrong, try again later",
                "numRows" => 0,
                "query" => $query,
                "data" => []
            ];
        }
        // $this->actionReturnData[] = $returnData;
        $this->saveTheQueryLog($query);
        return $returnData;
    }

    function queryInsert($query, $successMsg = "", $failedMsg = "")
    {
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data saved successfully",
                "insertedId" => $this->conn->insert_id,
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data saved failed, try again later",
                "insertedId" => "",
                "query" => $query
            ];
        }
        $this->actionReturnData[] = $returnData;

        $this->saveTheQueryLog($query);
        return $returnData;
    }

    function queryUpdate($query, $successMsg = "", $failedMsg = "")
    {
        $returnData = [];
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data modified successfully",
                "affectedRows" => $this->conn->affected_rows,
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data modified failed, try again later",
                "affectedRows" => 0,
                "query" => $query
            ];
        }

        $this->actionReturnData[] = $returnData;

        $this->saveTheQueryLog($query);
        return $returnData;
    }

    function queryDelete($query, $successMsg = "", $failedMsg = "")
    {
        $returnData = [];
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data deleted successfully",
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data deleted failed, try again later",
                "query" => $query
            ];
        }
        $this->actionReturnData[] = $returnData;

        $this->saveTheQueryLog($query);
        return $returnData;
    }

    function queryRollBack()
    {
        if ($this->isTransEnabled) {
            $this->conn->rollback();
            return [
                "status" => "success",
                "message" => "Roll back successfully",
                "data" => $this->actionReturnData
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Roll is not allowed, please allow to roll back.",
                "data" => $this->actionReturnData
            ];
        }
    }

    function queryFinish()
    {
        if ($this->isTransEnabled) {
            $isError = false;
            foreach ($this->actionReturnData as $key => $oneResponseData) {
                if ($oneResponseData["status"] != "success") {
                    $isError = true;
                }
            }
            if ($isError) {
                $this->conn->rollback();
                return [
                    "status" => "warning",
                    "message" => $this->actionErrorMsg != null ? $this->actionErrorMsg : ucfirst($this->actionName) . " failed, please try again.",
                    "data" => $this->actionReturnData
                ];
            } else {
                $this->conn->commit();
                return [
                    "status" => "success",
                    "message" => $this->actionSuccessMsg != null ? $this->actionSuccessMsg : ucfirst($this->actionName) . " success",
                    "data" => $this->actionReturnData
                ];
            }
        } else {
            return $this->actionReturnData[0];
        }
    }
    public function __destruct()
    {
        $this->conn->close();
    }
}


// Uses:
// ============================================
// $dbConObj = new Database(true);
// $dbConObj->setActionName("Item");
// $dbConObj->setSuccessMsg("Item successfully created");
// $dbConObj->setErrorMsg("Item creation failed");
// $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f1 '.time().'",`age`="24"');
// $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f2 '.time().'"');
// $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f3 '.time().'",`age`="24"');

// echo "<pre>";
// print_r($dbConObj->queryFinish());
// echo "</pre>";