<?php

require_once("jwt-token.php");



$jwtObj = new JwtToken();

$jwtToken = $jwtObj->createToken([
    "name"=>"rachhel",
    "id" => 3456
]);

echo $jwtToken."<br>";

echo"<pre>";
print_r($jwtObj->verifyToken($jwtToken));



?>