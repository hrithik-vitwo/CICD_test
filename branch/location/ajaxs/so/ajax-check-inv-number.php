<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];


function checkInvExists($number)
{
    $data = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE invoice_no ='" . $number . "' and status !='reverse'; ", true);

    if ($data['numRows'] > 0) {
        return true;
    }
    return false;
}


function checkFyPattern($string)
{
    $pattern = '/^FY-(\d{2})\/(\d{2})$/';
    if (preg_match($pattern, $string, $matches)) {
        $firstYear = intval($matches[1]);
        $secondYear = intval($matches[2]);
        return ($secondYear === $firstYear + 1);
    } else {
        return false;
    }
}

function isMonth($input)
{
    $monthNum = intval($input);
    return ($monthNum >= 1 && $monthNum <= 12);
}


function checkInvFormat($array, $inputString, $divider, $last_iv_number)
{
    $inputPrefix = "";
    $inputFY = "";
    $inputMonth = "";
    $inputSerial = "";
    $check = true;
    $response = [];

    if (key_exists('prefix', $array)) {
        $inputPrefix = substr($inputString, 0, strlen($array['prefix']));
        if ($inputPrefix === $array['prefix']) {
            // $response['prefix'] =  "prefix matched";
        } else {
            $response['prefix'] = "prefix does not match";
            $check = false;
        }
    }
    if (key_exists('fy', $array)) {
        $inputFY = substr($inputString, strlen($array['prefix']) + 1, strlen($array['fy']));
        if (checkFyPattern($inputFY)) {
            // $response['fy'] = "FY matched";
        } else {
            $response['fy'] = "FY does not match";
            $check = false;
        }
    }
    if (key_exists('month', $array)) {
        $inputMonth = substr($inputString, strrpos($inputString, $divider, strrpos($inputString, $divider) - strlen($inputString) - 1) + 1, 2);
        if (isMonth($inputMonth)) {
            // $response['month'] = "month matched";
        } else {
            $response['month'] = "month does not match";
            $check = false;
        }
    }
    if (key_exists('serial', $array)) {
        $inputSerial = substr($inputString, strrpos($inputString, $divider) + 1);
        // echo $inputSerial;
        // echo "ok ";
        // echo strlen($inputSerial);
        // echo " ok  ";
        // // echo $last_iv_number['serial'];
        // echo "okkk    ";
        // echo strlen($last_iv_number['serial']);
        if (intval($inputSerial) > 0 && strlen($inputSerial) == strlen($last_iv_number['serial'])) {
            // $response['serial'] ="serial matched";
        }
        else if(intval($inputSerial) > intval($last_iv_number['serial'])){
            $response['serial'] = "Serial number is greater";
            $check = false;

        } else {
            $response['serial'] = "serial does not match";
            $check = false;
        }
    } else {
        $response['serial'] = "serial required";
        $check = false;
    }


    if ($check) {
        if (checkInvExists($inputString)) {
            $response['status'] = 'warning';
        } else {
            $response['status'] = 'success';
        }
    } else {
        $response['status'] = 'error';
    }
    return $response;
}



if ($_POST['act'] == 'checkInvNumber') {

    $number = $_POST['number'];
    $iv_id = $_POST['iv_id'];


    $iv_query = queryGet("SELECT * FROM `erp_iv_varient` WHERE company_id=$company_id AND id=$iv_id AND status='active' ORDER BY id ASC;", true);

    $iv_queryData = $iv_query['data'][0];
    $verient_serialized = $iv_queryData['verient_serialized'];
    $unserlizedArray = unserialize($verient_serialized);
    $seperator = $iv_queryData['seperator'];
    $iv_number_example = $iv_queryData['iv_number_example'];
    $last_iv_number = unserialize($iv_queryData['last_inv_no']);


    $res = checkInvFormat($unserlizedArray, $number, $seperator, $last_iv_number);

    // console($res);
    // exit();

    echo json_encode($res);
}
