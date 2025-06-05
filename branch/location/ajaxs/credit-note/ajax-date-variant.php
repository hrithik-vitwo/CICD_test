<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
global $company_id;
global $admin_variant;

if (isset($_GET['vr']) && $_GET['vr'] != '') {

    $variant_id = $_GET['vr'];
    $status = 0;
    $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
    $check_var_data = $check_var_sql['data'];
    $max = $check_var_data['month_end'];
    $min = $check_var_data['month_start'];
    $month_var = date("m-Y", strtotime($max));

    $sql = queryGet("SELECT t1.postingDate AS prev_date FROM erp_credit_note t1 WHERE t1.company_id = $company_id AND t1.variant_id = $variant_id ORDER BY t1.postingDate DESC LIMIT 1;");

  $current_date = date("Y-m-d");
  $current_date_variant = date("m-Y");

  if ($sql['data']['prev_date']!=null) {
    // console("Both Dates is null");
    if($current_date_variant == $month_var)
    {
      $status = 1;
      $prev_date = $min;
      $next_date = $current_date;
    }
    elseif($month_var > $current_date_variant)
    {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
    else
    {
      $status = 1;
      $prev_date = $min;
      $next_date = $max;
    }
  } 
  else
  {
    $prevMonthVariant = date("m-Y", strtotime($sql['data']['prev_date']));
    if($month_var == $prevMonthVariant)
    {
      $status = 1;
      $prev_date = $sql['data']['prev_date'];
      $next_date = $current_date;
    }
    elseif($month_var > $prevMonthVariant)
    {
      if($month_var == $current_date_variant)
      {
        $status = 1;
        $prev_date = $min;
        $next_date = $current_date;
      }
      elseif($month_var > $current_date_variant)
      {
        $status = 0;
        $prev_date = '';
        $next_date = '';
      }
      else
      {
        $status = 1;
        $prev_date = $min;
        $next_date = $max;
      }
    }
    else
    {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
  }

  if ($sql['status'] == 'success') {

    $returnData['status'] = 'success';
    $returnData['message'] = 'ok';
    $returnData['start_date'] = $prev_date;
    $returnData['end_date'] = $next_date;
    $returnData['dateStatus'] = $status;
  } else {
    $returnData['status'] = 'warning';
    $returnData['message'] = 'something went wrong';
    $returnData['dateStatus'] = $status;
  }
}
else
{
    $returnData['status'] = 'warning';
    $returnData['message'] = 'Something went wrong';
    $returnData['dateStatus'] = 0;
}

echo json_encode($returnData);
