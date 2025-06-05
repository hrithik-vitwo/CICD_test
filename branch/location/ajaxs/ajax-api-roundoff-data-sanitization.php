<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

$dbObj = new Database();
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if ($_GET['act'] == "invData") {
        $returnObj = [];
        $sql = "SELECT * FROM erp_grninvoice AS grniv WHERE grniv.companyId=$company_id AND grniv.grnStatus='active' ORDER BY grniv.grnIvId DESC;";
        $queryRes = $dbObj->queryGet($sql, true);
        if ($queryRes['numRows'] > 0) {
            $dynamicData = [];
            $sl = 1;
            foreach ($queryRes['data'] as $data) {
                $gst = $data['grnTotalIgst'] + $data['grnTotalSgst'] + $data['grnTotalCgst'];

                $dynamicData[] = [
                    // "dataObj" => $data,
                    "sl" => $sl,
                    "grnIvId" => $data['grnIvId'],
                    "grnIvCode" => $data['grnIvCode'],
                    "grnCode" => $data['grnCode'],
                    "vendorCode" => $data['vendorCode'],
                    "vendorName" => $data['vendorName'],
                    "vendorDocumentNo" => $data['vendorDocumentNo'],
                    "vendorDocumentDate" => $data['vendorDocumentDate'],
                    "postingDate" => $data['postingDate'],
                    "grnCreatedAt" => $data['grnCreatedAt'],
                    "gst" => $gst,
                    "grnSubTotal" => $data['grnSubTotal']??0,
                    "grnTotalTds" => $data['grnTotalTds']??0,
                    "grnTotalTcs" => $data['grnTotalTcs']??0,
                    "roundoff" => $data['roundoff']??0,
                    "grnTotalAmount" => $data['grnTotalAmount']??0,
                    "dueAmt" => $data['dueAmt']??0,
                ];
                $sl++;
            }
            $returnObj = ["status" => "success", "msg" => "Success!", "data" => $dynamicData, "sql" => $sql];
        } else {
            $returnObj = ["status" => "warning", "msg" => "No data found!", "sql" => $sql,"data"=>[]];
        }
        echo json_encode($returnObj);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // if ($_POST['act'] == "updateRcmValue") {
    //     $returnObj = [];
    //     $id = $_POST['id'];
    //     $sql = "SELECT * FROM erp_grninvoice as inv WHERE inv.companyId=$company_id AND inv.rcm_enabled=1 AND inv.grnIvId=$id";
    //     $res = $dbObj->queryGet($sql, true);
    //     if ($res['numRows'] == 1) {
    //         $data = $res['data'][0];

    //         $sub = $data['grnSubTotal']??0;
    //         $tds = $data['grnTotalTds']??0;
    //         $tcs = $data['grnTotalTcs']??0;
            
    //         $roundoff = $data['roundoff']??0;

    //         $total = $data['grnTotalAmount'];
    //         $dueAmt = $data['dueAmt']??0;


    //         if($dueAmt<=0||$dueAmt==0){
    //             $returnObj=["status"=>"warning","msg"=>"due is zero cannot be updated"];
    //             echo json_encode($returnObj);
    //             exit();
    //         }
    //         $gst = $data['grnTotalIgst'] + $data['grnTotalSgst'] + $data['grnTotalCgst'];


    //         if (($sub + $tcs - $tds +$roundoff) == ($total - $gst)) {

    //             $newTotalAmount = $total - $gst;
    //             $newDueAmt = $dueAmt - $gst;

    //             if($newDueAmt<0){
    //                 $returnObj=["status"=>"warning","msg"=>"due is becoming zero cannot be updated"];
    //                 echo json_encode($returnObj);
    //                 exit();
    //             }

    //             $updateSql = "UPDATE erp_grninvoice SET grnTotalAmount = $newTotalAmount,dueAmt = $newDueAmt WHERE grnIvId = $id";
    //             $updateRes = $dbObj->queryUpdate($updateSql);
    //             if ($updateRes['status'] == 'success') {
    //                 $returnObj = ["status" => "success", "msg" => "Updated successfully", "sql" => $updateSql, "affected_rows" => $updateRes['affectedRows']];
    //             } else {
    //                 $returnObj = ["status" => "error", "msg" => "update failed!", "sql" => $updateSql];
    //             }
    //         }else{
    //             $returnObj = ["status" => "error", "msg" => "can not update!", "sql" => $sql,"valData"=>['left'=>$sub + $tcs - $tds, 'right'=>$total - $gst]];
    //         }
    //     } else if ($res['numRows'] == 0) {
    //         $returnObj = ["status" => "warning", "msg" => "No data found!", "sql" => $sql];
    //     } else {
    //         $returnObj = ["status" => "error", "msg" => "WRong Data Found!", "sql" => $sql];
    //     }
    //     echo json_encode($returnObj);
    // }
    
    if ($_POST['act'] == "updateRoundOff") {
        $returnObj = [];
        $id = $_POST['id'];
        $sql = "SELECT * FROM erp_grninvoice as inv WHERE inv.companyId=$company_id AND inv.grnIvId=$id";
        $res = $dbObj->queryGet($sql, true);
        if ($res['numRows'] == 1) {
            $data = $res['data'][0];

            $sub = $data['grnSubTotal']??0;
            $tds = $data['grnTotalTds']??0;
            $tcs = $data['grnTotalTcs']??0;
            
            $roundoff = $data['roundoff']??0;

            $total = $data['grnTotalAmount'];
            $dueAmt = $data['dueAmt'];

            $gst = $data['grnTotalIgst'] + $data['grnTotalSgst'] + $data['grnTotalCgst'];

            if($dueAmt<=0||$dueAmt==0){
                $returnObj=["status"=>"warning","msg"=>"due is zero cannot be updated"];
                echo json_encode($returnObj);
                exit();
            }

            $rcm=$data['rcm_enabled']??0;

            $leftSide=($sub + $tcs - $tds);

            if($rcm==0){
                $leftSide=$leftSide+$gst;
            }
            $rightSide=($total +$roundoff);

            if ($leftSide!=$rightSide) {
                $newTotalAmount = $total + $roundoff;
                $newDueAmt = $dueAmt + $roundoff;

                if($newDueAmt<0){
                    $returnObj=["status"=>"error","msg"=>"due is becoming zero cannot be updated"];
                    echo json_encode($returnObj);
                    exit();
                }
                $updateSql = "UPDATE erp_grninvoice SET grnTotalAmount = $newTotalAmount,dueAmt = $newDueAmt WHERE grnIvId = $id";
                $updateRes = $dbObj->queryUpdate($updateSql);
                if ($updateRes['status'] == 'success') {
                    $returnObj = ["status" => "success", "msg" => "Updated successfully", "sql" => $updateSql, "affected_rows" => $updateRes['affectedRows']];
                } else {
                    $returnObj = ["status" => "error", "msg" => "update failed!", "sql" => $updateSql];
                }
            }else{
                $returnObj = ["status" => "error", "msg" => "can not update!", "sql" => $sql,"valData"=>['left'=>$leftSide, 'right'=>$rightSide]];
            }
        } else if ($res['numRows'] == 0) {
            $returnObj = ["status" => "warning", "msg" => "No data found!", "sql" => $sql];
        } else {
            $returnObj = ["status" => "error", "msg" => "WRong Data Found!", "sql" => $sql];
        }
        echo json_encode($returnObj);
    }
    
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
