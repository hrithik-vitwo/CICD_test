<?php
require_once("../../../app/v1/connection-branch-admin.php");


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $component_type = $_GET['component_type'];

    switch ($component_type) {
        case "customer":
            require_once("../components/view/customer-view.php");   
            break;
        }
}
