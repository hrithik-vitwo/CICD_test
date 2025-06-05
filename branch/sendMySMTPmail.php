<?php

require_once("../app/v1/connection-admin.php");

$tt="rguria@vitwo.in";
$ss="SMTP TEMPLATE2";
$mm='checked htML message body Ramen. <b>Gmail</b> SMTP email body.';

if(SendMailByMySMTPmailTemplate($tt,$ss,$mm,1)){
	echo "success";
}else{
	echo "fail.";
}


?>
