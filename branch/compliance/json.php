<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("controller/gstr1-json-repositary-controller-test.php");



$gstr1JsonRepoObj = new Gstr1JsonRepository("072024", "2024-07-01", "2024-07-31");
$jsonObj = $gstr1JsonRepoObj->generate();
console($jsonObj);