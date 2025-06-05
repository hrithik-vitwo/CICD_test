<?php
$company_id=1;
$branch_id=1;
$location_id=1;
$created_by="Admin";
$updated_by="Branch";

include(__DIR__."/autoload.php");

class TestController extends BaseController{
    
    function addCustomer($data=[]){
        $this->dbStart();
        return $this->dbInsert('INSERT INTO `complexity_test` SET `companyId`=1, `locationName`="TT",`qty`=23,`openStock`=5,`reserveStock`=5');
        // return $this->dbStatus();
    }
}

echo "<pre>";

$testController = new TestController();
// $testController->dbGlobalRollBackStart();
$actionObj = $testController->addCustomer(["name" => "rachhell40", "age"=>30, "gender" => "male"]);


print_r($actionObj);


if($actionObj["status"]=="success"){

    // print_r($testController->dbGlobalCommit());


}
echo "</pre>";


?>