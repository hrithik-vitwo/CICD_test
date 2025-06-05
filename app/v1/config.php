<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

//error_reporting(E_ALL);
error_reporting(1);

$PROJECT_MODE = "QA"; // LOCAL / QA / A2-1 / A2
if ($PROJECT_MODE == "A2"||$PROJECT_MODE=="A3") {
  $hostName = "one.vitwo.ai";
  $userName = "vitwo_one_user";
  $databasePass = "VitwoOneDb@12345";
  $databaseName = "vitwo_one";
  if($PROJECT_MODE=="A3"){
    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/a3/");
  }else{
    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/a2/");
  }
} else if ($PROJECT_MODE == "A2-1") {
  $hostName = "one.vitwo.ai";
  $userName = "vitwo_one_user";
  $databasePass = "VitwoOneDb@12345";
  $databaseName = "vitwo_one";

  define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/A2-1/");
} else if ($PROJECT_MODE == "QA") {
  $hostName = "one.vitwo.ai";
  $userName = "vitwo_one_user";
  $databasePass = "VitwoOneDb@12345";
  $databaseName = "vitwo_one";

  define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/q1/");
} else {
  //For Local Host DB
  // $hostName = "localhost";
  // $userName = "root";
  // $databasePass = "";
  // $databaseName = "devalpha";
  // $hostName = "one.vitwo.ai";
  // $userName = "vitwo_one_user";
  // $databasePass = "VitwoOneDb@12345";
  // $databaseName = "vitwo_one";

  $hostName = "192.168.0.250";
  $userName = "localuser";
  $databasePass = "Local@12345678";
  $databaseName = "devalpha";
  
  // $hostName = "one.vitwo.ai";
  // $userName = "vitwo_one_user";
  // $databasePass = "VitwoOneDb@12345";
  // $databaseName = "vitwo_one";
  
  define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/vitwoai-alpha/");
}
// Create connection
$dbCon = mysqli_connect($hostName, $userName, $databasePass, $databaseName);

// Check connection
if (!$dbCon) {
  die("Connection failed: " . mysqli_connect_error());
  exit();
} else {
  //change the time zone of phpmyadmin
  if (mysqli_query($dbCon, "SET time_zone='+5:30'")) {
    //echo "changed time zone<br>";
  } else {
    echo "<p style='color:red'>Phpmyadmin time_zone not changed :(</p><br>";
  }
}

define("BASE_DIR", dirname(__DIR__, 2) . "/");

if ($PROJECT_MODE == "A2" || $PROJECT_MODE == "A2-1") {
  $old_substring = 'a2/';
  $baseDir = str_replace($old_substring, '', BASE_DIR);
  $baseUrl = str_replace($old_substring, '', BASE_URL);

  define("BUCKET_DIR", $baseDir);
  define("BUCKET_URL", $baseUrl);
}

if ($PROJECT_MODE == "A3") {
  $old_substring = 'a3/';
  $baseDir = str_replace($old_substring, '', BASE_DIR);
  $baseUrl = str_replace($old_substring, '', BASE_URL);

  define("BUCKET_DIR", $baseDir);
  define("BUCKET_URL", $baseUrl);
}


if ($PROJECT_MODE == "QA") {
  $old_substring = 'q1/';
  $baseDir = str_replace($old_substring, '', BASE_DIR);
  $baseUrl = str_replace($old_substring, '', BASE_URL);

  define("BUCKET_DIR", $baseDir);
  define("BUCKET_URL", $baseUrl);
} else {
  define("BUCKET_DIR", BASE_DIR);
  define("BUCKET_URL", BASE_URL);
}

define("ADMIN_URL", BASE_URL . "admin/");
define("COMPANY_URL", BASE_URL . "company/");
define("BRANCH_URL", BASE_URL . "branch/");
define("LOCATION_URL", BRANCH_URL . "location/");
define("VENDOR_URL", BASE_URL . "vendor/");
define("CUSTOMER_URL", BASE_URL . "customer/");
define("WEB_ADMIN_URL", BASE_URL . "webmaster/");
define("SERVICE_URL", BASE_URL."service/");
require_once("database.php");
