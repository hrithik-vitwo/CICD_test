<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/company/func-branches.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../boq/controller/boq.controller.php");
$goodsController = new GoodsController();
$boqControllerObj = new BoqController();

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modalData') {

    $itemId = $_GET['itemId'];
    // $sql_list = "";
    // $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $num_list = $sqlMainQryObj['numRows'];
    $boqDetailObj = $boqControllerObj->getBoqDetails($itemId);

    // console($boqDetailObj);
    
    $dynamic_data = [];
    
    if ($boqDetailObj['status']=="success" ) {
        // $data = $sqlMainQryObj['data'];

        $dynamic_data = [
            // "dataObj" => $data,
            "boqDetailObj"=>$boqDetailObj['data'],
            "companyCurrency" => getSingleCurrencyType($company_currency),
            // "created_by" => getCreatedByUser($data['createdBy']),
            // "created_at" => formatDateORDateTime($data['createdAt']),
            // "updated_by" => getCreatedByUser($data['updatedBy']),
            // "updated_at" => formatDateORDateTime($data['updatedAt']),
        ];


        $res = [
            "status" => true,
            "msg" => "Success",
            // "sql_list" => $sql_list,
            "data" => $dynamic_data
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