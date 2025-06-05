<?php
require_once dirname(__DIR__)."/connection-branch-admin.php";
require_once dirname(__DIR__)."/functions/branch/func-brunch-so-controller.php";
require_once dirname(__DIR__)."/functions/branch/func-subscription-invoice.php";

// echo 1;
// console("Subscription service invoice"); 
//exit();
 
$BranchSoObj = new SubscriptionController();


$subscriptionServiceInv = $BranchSoObj->subscriptionInvoice(); 


?> 