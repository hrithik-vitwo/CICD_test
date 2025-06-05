<?php 
require_once("../../app/v1/connection-branch-admin.php");
$id=base64_decode($_GET["id"]);
$company_id = base64_decode($_GET["c_id"]);
$show=0;
$checksql=queryGet("SELECT isMailValid FROM ".ERP_CUSTOMER." WHERE customer_id=".$id."");
$status=$checksql["data"]["isMailValid"];
if($status=="no"){
    $show=1;
    $query=queryUpdate("UPDATE ".ERP_CUSTOMER." SET `isMailValid` = 'yes' WHERE `customer_id` = ".$id." AND `company_id` = ".$company_id."");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("favicon"); ?>">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .verification-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            width: 400px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .verification-box img {
            width: 70px;
            margin-bottom: 20px;
            opacity: 0;
            transform: scale(0.5);
            animation: bounceIn 0.8s ease-out forwards, pulse 1.5s infinite ease-in-out 1s;
        }
        .verification-box h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .verification-box p {
            font-size: 16px;
            color: #666;
        }
        /* Hover Glow Effect */
        .verification-box:hover {
            box-shadow: 0px 0px 15px rgba(0, 150, 255, 0.6);
            transition: box-shadow 0.4s ease-in-out;
        }
        /* Green Checkmark Glow on Hover */
        .verification-box img:hover {
            filter: drop-shadow(0 0 10px rgba(0, 255, 0, 0.8));
        }
        /* Bounce-in Animation */
        @keyframes bounceIn {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        /* Pulse Effect */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body>
    <?php if($show==1){?>
         <div class="verification-box" id="verifyBox">
         <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="Verified Icon">
         <h2>Email Verification</h2>
         <p>Your email has been verified. You can continue using the application.</p>
     </div>
   <?php }else{ ?>
    <div class="verification-box" id="verifyBox">
         <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="Verified Icon">
         <h2>Alredy Verified</h2>
         <p>Your email alredy verified. You can continue using the application.</p>
     </div>
   <?php } ?>
   
   <script>
        // Zoom-in effect when page loads
        document.addEventListener("DOMContentLoaded", function () {
            let box = document.getElementById("verifyBox");
            box.style.opacity = "1";
            box.style.transform = "scale(1)";
        });
    </script>
</body>
</html>
