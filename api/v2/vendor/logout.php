<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_code = $_POST['company_code'];

    return sendApiResponse([
        "status" => "success",
        "message" => "Logout Successfull",
        "token" => ""
    ], 200);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
