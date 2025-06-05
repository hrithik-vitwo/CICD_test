<?php
class CoreDatabase
{
    // ========================== DB CRED ========================= //
    private $DB_HOST = 'localhost';
    private $DB_USER = 'devalpha';
    private $DB_PASS = 'NPkY2T0gIKqCWCe';
    private $DB_NAME = 'devalpha';
    private $DB_TIMEZONE = '+5:30';
    // ============================================================ //
    // private $DB_HOST = 'localhost';
    // private $DB_USER = 'root';
    // private $DB_PASS = '';
    // private $DB_NAME = 'test';
    // private $DB_TIMEZONE = '+5:30';
    // ============================================================ //
    private $conn;
    private $globalRollBackFlag = false;
    private $localRollbackFlag = false;
    private $actionName = "Data";
    private $actionSuccessMsg = "Data successfully proccessed";
    private $actionErrorMsg = "Data proccessed failed, please try again";
    private $actionReturnData = [];

    protected function dbStart($enableRollback = false)
    {
        $this->conn = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        } else {
            $this->conn->query("SET time_zone='" . $this->DB_TIMEZONE . "'");
            if ($enableRollback) {
                $this->localRollbackFlag = true;
                $this->conn->begin_transaction();
                return true;
            }
            return true;
        }
        return false;
    }

    protected function dbActionName($actionName = "Data")
    {
        $this->actionName = $actionName;
    }

    protected function dbSuccessMsg($successMsg = "Data successfully proccessed")
    {
        $this->actionSuccessMsg = $successMsg;
    }

    protected function dbErrorMsg($errorMsg = "Data proccessed failed, please try again")
    {
        $this->actionErrorMsg = $errorMsg;
    }
    protected function dbGet($query, $isMultipleRows = false)
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
        return $returnData;
    }

    protected function dbInsert($query, $successMsg = "", $failedMsg = "")
    {
        $rollbackMessage = ($this->localRollbackFlag || $this->globalRollBackFlag) ? "Commit or rollback is requied to complete the proccess" : "Commit or rollback is not requied";
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data saved successfully",
                "insertedId" => $this->conn->insert_id,
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data saved failed, try again later",
                "insertedId" => "",
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        }
        $this->actionReturnData[] = $returnData;
        return $returnData;
    }

    protected function dbUpdate($query, $successMsg = "", $failedMsg = "")
    {
        $returnData = [];
        $rollbackMessage = ($this->localRollbackFlag || $this->globalRollBackFlag) ? "Commit or rollback is requied to complete the proccess" : "Commit or rollback is not requied";
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data modified successfully",
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data modified failed, try again later",
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        }

        $this->actionReturnData[] = $returnData;
        return $returnData;
    }

    protected function dbDelete($query, $successMsg = "", $failedMsg = "")
    {
        $returnData = [];
        $rollbackMessage = ($this->localRollbackFlag || $this->globalRollBackFlag) ? "Commit or rollback is requied to complete the proccess" : "Commit or rollback is not requied";
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data deleted successfully",
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data deleted failed, try again later",
                "rollback"=>$rollbackMessage,
                "query" => $query
            ];
        }
        $this->actionReturnData[] = $returnData;
        return $returnData;
    }

    public function dbStatus(){
        $isError = false;
        foreach ($this->actionReturnData as $key => $oneResponseData) {
            if ($oneResponseData["status"] != "success") {
                $isError=true;
                break;
            }
        }
        if($this->localRollbackFlag){
            if($this->globalRollBackFlag){
                $rollbackMessage = "Global Rollback or Commit is required to complete the process";
            }else{
                if($isError){
                    $this->conn->rollback();
                }else{
                    $this->conn->commit();
                }
                $rollbackMessage = "Proccess is completed, Global Rollback or Commit is not required";
            }
        }else{
            $rollbackMessage = "Local Rollback is not enabled, no need to do anything";
        }

        if($isError){
            return [
                "status" => "warning",
                "message" => $this->actionErrorMsg != null ? $this->actionErrorMsg : ucfirst($this->actionName) . " failed, please try again.",
                "rollback" => $rollbackMessage,
                "data" => $this->actionReturnData
            ];
        }else{
            return [
                "status" => "success",
                "message" => $this->actionSuccessMsg != null ? $this->actionSuccessMsg : ucfirst($this->actionName) . " success",
                "rollback" => $rollbackMessage,
                "data" => $this->actionReturnData
            ];
        }
    }

    public function dbGlobalRollBackStart(){
        $this->globalRollBackFlag = true;
    }
    public function dbGlobalRollback()
    {
        if ($this->globalRollBackFlag) {
            if($this->localRollbackFlag){
                $this->conn->rollback();
                $rollbackMessage = "Global Rollback success and process completed";
            }else{
                $rollbackMessage = "Global Rollback failed because the local Rollback is not enabled";
            }
            return [
                "status" => "warning",
                "message" => $this->actionErrorMsg != null ? $this->actionErrorMsg : ucfirst($this->actionName) . " failed, please try again.",
                "rollback" => $rollbackMessage,
                "data" => $this->actionReturnData
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Global Rollback is not enabled, please enable first before rollback.",
                "rollback" => "Global Rollback is not enabled,  please enable first before rollback.",
                "data" => $this->actionReturnData
            ];
        }
    }
    public function dbGlobalCommit(){
        if ($this->globalRollBackFlag) {
            if($this->localRollbackFlag){
                $this->conn->commit();
                $rollbackMessage = "Global committed success and process completed";
            }else{
                $rollbackMessage = "Global committed failed because the local rollback is not enabled";
            }
            return [
                "status" => "success",
                "message" => $this->actionSuccessMsg != null ? $this->actionSuccessMsg : ucfirst($this->actionName) . " success",
                "rollback" => $rollbackMessage,
                "data" => $this->actionReturnData
            ];
        } else {
            return [
                "status" => "success",
                "message" => $this->actionSuccessMsg != null ? $this->actionSuccessMsg : ucfirst($this->actionName) . " success",
                "rollback" => "Global rollback is not enabled, please enable first and then commit",
                "data" => $this->actionReturnData
            ];
        }
    }
    function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}