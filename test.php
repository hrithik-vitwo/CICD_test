<?php


phpinfo();

exit();

define("COMP_STORAGE_DIR", "/home/devalpha/public_html/uploads/4");
define("COMP_STORAGE_URL", "http://devalpha.vitwo.ai/uploads/4");

function createCompanyUploadDirs()
{
    $compStorageDir = COMP_STORAGE_DIR;
    $parentDir = dirname($compStorageDir);
    if (is_writable($parentDir)) {
        if (!is_dir($compStorageDir)) {
            mkdir($compStorageDir);
        }
        if (!is_dir($compStorageDir . "/profile")) {
            mkdir($compStorageDir . "/profile");
        }
        if (!is_dir($compStorageDir . "/cancelled-cheque")) {
            mkdir($compStorageDir . "/cancelled-cheque");
        }
        if (!is_dir($compStorageDir . "/grn-invoices")) {
            mkdir($compStorageDir . "/grn-invoices");
        }
        if (!is_dir($compStorageDir . "/visiting-card")) {
            mkdir($compStorageDir . "/visiting-card");
        }
        return [
            "status" => "success",
            "message" => "All dir creation was successful"
        ];
    } else {
        return [
            "status" => "warning",
            "message" => "Dir is not created, please provide writable permissions to '" . $parentDir . "' folder"
        ];
    }
}

// print_r(createCompanyUploadDirs());

class Dbcon
{
    private $conn;
    private $isTransEnabled = false;
    private $actionName = "Data";
    private $actionSuccessMsg = "Data saved successfully";
    private $actionErrorMsg = "Data saved failed, please try again";
    private $actionReturnData = [];

    function __construct($isTransEnabled = false)
    {
        $this->isTransEnabled = $isTransEnabled;
        $this->conn = new mysqli("localhost", "devalpha", "NPkY2T0gIKqCWCe", "devalpha");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        } else {
            if ($this->isTransEnabled) {
                $this->conn->begin_transaction();
            }
        }
    }
    function setActionName($actionName="Data"){
        $this->actionName = $actionName;
    }
    function setSuccessMsg($successMsg="Data successfully created"){
        $this->actionSuccessMsg = $successMsg;
    }
    function setErrorMsg($errorMsg="Data creation failed, please try again"){
        $this->actionErrorMsg = $errorMsg;
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
        $this->actionReturnData[] = $returnData;
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
        return $returnData;
    }

    function queryUpdate($query, $successMsg = "", $failedMsg = "")
    {
        $returnData = [];
        if ($this->conn->query($query)) {
            $returnData = [
                "status" => "success",
                "message" => $successMsg != "" ? $successMsg : "Data modified successfully",
                "query" => $query
            ];
        } else {
            $returnData = [
                "status" => "failed",
                "message" => $failedMsg != "" ? $failedMsg : "Data modified failed, try again later",
                "query" => $query
            ];
        }

        $this->actionReturnData[] = $returnData;
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
        return $returnData;
    }


    function queryFinish(){
        if ($this->isTransEnabled){
            $isError = false;
            foreach($this->actionReturnData as $key=>$oneResponseData){
                if($oneResponseData["status"]!="success"){
                    $isError = true;
                }
            }
            if($isError){
                $this->conn->rollback();
                return [
                    "status" => "warning",
                    "message" => $this->actionErrorMsg!=null ? $this->actionErrorMsg : ucfirst($this->actionName)." failed, please try again.",
                    "data" => $this->actionReturnData
                ];
            }else{
                $this->conn->commit();
                return [
                    "status" => "success",
                    "message" => $this->actionSuccessMsg!=null ? $this->actionSuccessMsg : ucfirst($this->actionName)." success",
                    "data" => $this->actionReturnData
                ];
            }
        }else{
            return $this->actionReturnData[0];
        }
    }
    public function __destruct() {
        $this->conn->close();
    }
}

// $dbConObj = new Dbcon(true);

// $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f1 '.time().'",`age`="24"');
// // $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f2 '.time().'"');
// $dbConObj->queryInsert('INSERT `test` SET `name`="Kashif f3 '.time().'",`age`="24"');

// echo "<pre>";
// print_r($dbConObj->queryFinish());
// echo "</pre>";
?>

