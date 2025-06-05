<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");






// API CODE
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $jwtObj = new JwtToken();
    $jwtToken = $jwtObj->createToken([
        "name"=>"rachhel",
        "id" => 3456
    ]);


    sendApiResponse(200,[
        "status" => "success",
        "token" => $jwtToken,
        "data" => [
            "vendorCode"=>87654
        ]
    ]);

    // echo $jwtToken."<br>";
    // echo"<pre>";
    // print_r($jwtObj->verifyToken($jwtToken));


}else{
    sendApiResponse(405,[
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ]);
}
?>