<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act']=='modaldata') {
    $poId = $_GET[''];
    $cond = "";
    $sql_list = "";

    $sqlMainQryObj = $dbObj->queryGet($sql_list);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        // navBar Button 
        $navBtn='';
        if ($data['po_status'] == 9) {
            $navBtn = "";
            $btn .='<a class="nav-link btn btn-primary" id="" data-toggle="" href="manage-manual-grn.php?view='. $data['po_number'].'&type=grn">GRN</a>';
            $navBtn = '<div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar">' . $btn . '</div>';
    
        }


        $dynamic_data = [
            
            "navBtn" =>$navBtn
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "sql_list" => $sql_list
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
}