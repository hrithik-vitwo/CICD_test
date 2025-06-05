<?php
// Start the session
session_start();

// Construct the base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$path = dirname($script);
$base_url = $protocol . $host . '/';

// Unset session variable and destroy the session
unset($_SESSION["logedBranchAdminInfo"]);
session_destroy();

// Function to handle redirection
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check the 'v' parameter and redirect accordingly
if (isset($_GET['v']) && $_GET['v'] == 1) {
    redirect($base_url . "index.php");
    echo '1'; // This will not be executed due to the exit in the redirect function
} else {
    redirect($base_url . "a2/index.php");
    echo '2'; // This will not be executed due to the exit in the redirect function
}
?>
